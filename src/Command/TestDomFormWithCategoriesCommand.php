<?php

namespace App\Command;

use App\Service\Dom\DomCacheService;
use App\Repository\Dom\DomSousTypeDocumentRepository;
use App\Dto\Dom\DomFirstFormData;
use App\Form\Dom\DomFirstFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Form\FormFactoryInterface;

class TestDomFormWithCategoriesCommand extends Command
{
    protected static $defaultName = 'app:test-dom-form-categories';
    protected static $defaultDescription = 'Test l\'affichage des catégories dans le formulaire DOM';

    private DomCacheService $cacheService;
    private DomSousTypeDocumentRepository $sousTypeRepo;
    private EntityManagerInterface $em;
    private FormFactoryInterface $formFactory;

    public function __construct(
        DomCacheService $cacheService,
        DomSousTypeDocumentRepository $sousTypeRepo,
        EntityManagerInterface $em,
        FormFactoryInterface $formFactory
    ) {
        $this->cacheService = $cacheService;
        $this->sousTypeRepo = $sousTypeRepo;
        $this->em = $em;
        $this->formFactory = $formFactory;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('🧪 Test du formulaire DOM avec catégories...');
        $output->writeln('==========================================');

        try {
            // 1. Créer un DTO avec le sous-type MISSION
            $output->writeln('1. Création du DTO avec sous-type MISSION...');
            $dto = new DomFirstFormData($this->sousTypeRepo, null, $this->cacheService, $this->em);

            if ($dto->getSousTypeDocument()) {
                $output->writeln("   ✓ Sous-type chargé: {$dto->getSousTypeDocument()->getCodeSousType()}");
            } else {
                $output->writeln('   ✗ Aucun sous-type chargé');
                return Command::FAILURE;
            }
            $output->writeln('');

            // 2. Créer le formulaire
            $output->writeln('2. Création du formulaire...');
            $form = $this->formFactory->create(DomFirstFormType::class, $dto);
            $output->writeln("   ✓ Formulaire créé avec " . count($form->all()) . " champs");

            // Simuler l'événement POST_SET_DATA
            $output->writeln('   Simulation de l\'événement POST_SET_DATA...');
            $event = new \Symfony\Component\Form\FormEvent($form, $dto);
            $form->getConfig()->getEventDispatcher()->dispatch($event, \Symfony\Component\Form\FormEvents::POST_SET_DATA);
            $output->writeln("   ✓ Événement POST_SET_DATA simulé, formulaire a maintenant " . count($form->all()) . " champs");
            $output->writeln('');

            // 3. Vérifier si le champ catégorie existe
            $output->writeln('3. Vérification du champ catégorie...');
            if ($form->has('categorie')) {
                $categorieField = $form->get('categorie');
                $choices = $categorieField->getConfig()->getOption('choices');
                $output->writeln("   ✓ Champ catégorie trouvé avec " . count($choices) . " options:");
                foreach ($choices as $choice) {
                    $output->writeln("     - {$choice->getDescription()}");
                }
            } else {
                $output->writeln('   ✗ Champ catégorie non trouvé');

                // Vérifier pourquoi le champ n'est pas ajouté
                $output->writeln('   Debug: Vérification des conditions...');
                $sousTypeDocument = $dto->getSousTypeDocument();
                $agenceCode = '01'; // Code d'agence par défaut

                $output->writeln("   - Sous-type document: " . ($sousTypeDocument ? $sousTypeDocument->getCodeSousType() : 'null'));
                $output->writeln("   - Code agence: $agenceCode");

                // Test de la requête des catégories
                $rmqDescription = str_starts_with($agenceCode, '50') ? '50' : 'STD';
                $output->writeln("   - RMQ description: $rmqDescription");

                $categories = $this->em->createQueryBuilder()
                    ->select('DISTINCT c')
                    ->from(\App\Entity\Dom\DomCategorie::class, 'c')
                    ->join('c.domIndemnites', 'i')
                    ->join('i.domRmqId', 'r')
                    ->where('i.domSousTypeDocumentId = :sousTypeDoc')
                    ->andWhere('r.description = :rmqDescription')
                    ->setParameter('sousTypeDoc', $sousTypeDocument)
                    ->setParameter('rmqDescription', $rmqDescription)
                    ->getQuery()
                    ->getResult();

                $output->writeln("   - Catégories trouvées: " . count($categories));
                foreach ($categories as $categorie) {
                    $output->writeln("     - {$categorie->getDescription()}");
                }
            }
            $output->writeln('');

            // 4. Test de la vue du formulaire
            $output->writeln('4. Test de la vue du formulaire...');
            $formView = $form->createView();
            $output->writeln("   ✓ Vue du formulaire créée");

            // Vérifier si le champ catégorie est dans la vue
            if (isset($formView->children['categorie'])) {
                $output->writeln('   ✓ Champ catégorie présent dans la vue');
            } else {
                $output->writeln('   ✗ Champ catégorie absent de la vue');
            }
            $output->writeln('');

            $output->writeln('🎉 Test terminé !');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('❌ Erreur: ' . $e->getMessage());
            $output->writeln('Stack trace:');
            $output->writeln($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
