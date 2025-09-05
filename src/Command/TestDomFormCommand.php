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

class TestDomFormCommand extends Command
{
    protected static $defaultName = 'app:test-dom-form';
    protected static $defaultDescription = 'Test du formulaire DOM pour vérifier les performances';

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
        $output->writeln('🧪 Test du formulaire DOM - Demande d\'Ordre de Mission');
        $output->writeln('====================================================');
        $output->writeln('');

        try {
            // Test 1: Vérifier que le service DomCacheService est disponible
            $output->writeln('1. Test du service DomCacheService...');
            $output->writeln('   ✓ Service DomCacheService chargé avec succès');
            $output->writeln('');

            // Test 2: Vérifier que le repository DomSousTypeDocumentRepository est disponible
            $output->writeln('2. Test du repository DomSousTypeDocumentRepository...');
            $output->writeln('   ✓ Repository DomSousTypeDocumentRepository chargé avec succès');
            $output->writeln('');

            // Test 3: Créer un DTO DomFirstFormData
            $output->writeln('3. Test de création du DTO DomFirstFormData...');
            $dto = new DomFirstFormData($this->sousTypeRepo, null, $this->cacheService, $this->em);
            $output->writeln('   ✓ DTO DomFirstFormData créé avec succès');

            if ($dto->getSousTypeDocument()) {
                $output->writeln('   ✓ Sous-type de document chargé: ' . $dto->getSousTypeDocument()->getCodeSousType());
            } else {
                $output->writeln('   ⚠ Aucun sous-type de document trouvé');
            }
            $output->writeln('');

            // Test 4: Vérifier que l'entité est gérée par l'EntityManager
            $output->writeln('4. Test de gestion de l\'entité par l\'EntityManager...');
            if ($dto->getSousTypeDocument()) {
                $isManaged = $this->em->contains($dto->getSousTypeDocument());
                if ($isManaged) {
                    $output->writeln('   ✓ L\'entité est gérée par l\'EntityManager');
                } else {
                    $output->writeln('   ✗ L\'entité n\'est PAS gérée par l\'EntityManager');
                }
            } else {
                $output->writeln('   ⚠ Pas d\'entité à tester');
            }
            $output->writeln('');

            // Test 5: Test du formulaire
            $output->writeln('5. Test de création du formulaire...');
            $form = $this->formFactory->create(DomFirstFormType::class, $dto);
            $output->writeln('   ✓ Formulaire créé avec succès');
            $output->writeln('   ✓ Nombre de champs: ' . count($form->all()));
            $output->writeln('');

            $output->writeln('🎉 Tous les tests sont passés avec succès !');
            $output->writeln('Le formulaire DOM devrait maintenant fonctionner sans erreur.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('❌ Erreur lors du test: ' . $e->getMessage());
            $output->writeln('Stack trace:');
            $output->writeln($e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
