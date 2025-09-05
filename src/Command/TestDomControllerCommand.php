<?php

namespace App\Command;

use App\Service\Dom\DomCacheService;
use App\Repository\Dom\DomSousTypeDocumentRepository;
use App\Service\Dom\DomWizardManager;
use App\Controller\Dom\DomFirstFormController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class TestDomControllerCommand extends Command
{
    protected static $defaultName = 'app:test-dom-controller';
    protected static $defaultDescription = 'Test le contrôleur DOM pour vérifier l\'affichage des catégories';

    private DomCacheService $cacheService;
    private DomSousTypeDocumentRepository $sousTypeRepo;
    private EntityManagerInterface $em;
    private FormFactoryInterface $formFactory;
    private DomWizardManager $wizardManager;
    private Security $security;

    public function __construct(
        DomCacheService $cacheService,
        DomSousTypeDocumentRepository $sousTypeRepo,
        EntityManagerInterface $em,
        FormFactoryInterface $formFactory,
        DomWizardManager $wizardManager,
        Security $security
    ) {
        $this->cacheService = $cacheService;
        $this->sousTypeRepo = $sousTypeRepo;
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->wizardManager = $wizardManager;
        $this->security = $security;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('🧪 Test du contrôleur DOM...');
        $output->writeln('==========================');

        try {
            // 1. Créer un contrôleur DOM
            $output->writeln('1. Création du contrôleur DOM...');
            $controller = new DomFirstFormController();
            $output->writeln('   ✓ Contrôleur créé');
            $output->writeln('');

            // 2. Simuler une requête
            $output->writeln('2. Simulation d\'une requête...');
            $request = new Request();
            $output->writeln('   ✓ Requête simulée');
            $output->writeln('');

            // 3. Créer un utilisateur de test (simulé)
            $output->writeln('3. Création d\'un utilisateur de test...');
            $user = new \App\Entity\Admin\PersonnelUser\User();
            $user->setUsername('testuser');
            $user->setRoles(['ROLE_USER']);

            // Créer un personnel avec agence
            $personnel = new \App\Entity\Admin\PersonnelUser\Personnel();
            $personnel->setNom('Test');
            $personnel->setPrenoms('User');
            $personnel->setMatricule(12345);
            $user->setPersonnel($personnel);

            $output->writeln('   ✓ Utilisateur de test créé');
            $output->writeln('');

            // 4. Tester la méthode createOrRestoreDto
            $output->writeln('4. Test de la méthode createOrRestoreDto...');
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('createOrRestoreDto');
            $method->setAccessible(true);

            $dto = $method->invoke($controller, $this->wizardManager, $this->sousTypeRepo, $this->em, $this->cacheService);

            if ($dto->getSousTypeDocument()) {
                $output->writeln("   ✓ DTO créé avec sous-type: {$dto->getSousTypeDocument()->getCodeSousType()}");
            } else {
                $output->writeln('   ✗ DTO créé sans sous-type');
            }
            $output->writeln('');

            // 5. Tester la création du formulaire avec l'utilisateur
            $output->writeln('5. Test de la création du formulaire avec utilisateur...');

            // Simuler l'utilisateur connecté
            $this->security->getToken()->setUser($user);

            $form = $this->formFactory->create(\App\Form\Dom\DomFirstFormType::class, $dto);
            $output->writeln("   ✓ Formulaire créé avec " . count($form->all()) . " champs");

            // Vérifier si le champ catégorie existe
            if ($form->has('categorie')) {
                $categorieField = $form->get('categorie');
                $choices = $categorieField->getConfig()->getOption('choices');
                $output->writeln("   ✓ Champ catégorie trouvé avec " . count($choices) . " options:");
                foreach ($choices as $choice) {
                    $output->writeln("     - {$choice->getDescription()}");
                }
            } else {
                $output->writeln('   ✗ Champ catégorie non trouvé');
            }
            $output->writeln('');

            // 6. Test de la vue du formulaire
            $output->writeln('6. Test de la vue du formulaire...');
            $formView = $form->createView();

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
