<?php

namespace App\Command;

use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateTestUserCommand extends Command
{
    protected static $defaultName = 'app:create-test-user';
    protected static $defaultDescription = 'Crée un utilisateur de test avec une agence pour tester le DOM';

    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('👤 Création d\'un utilisateur de test...');

        try {
            // 1. Créer une agence de test
            $output->writeln('1. Création de l\'agence de test...');
            $agence = $this->em->getRepository(Agence::class)->findOneBy(['code' => '01']);
            if (!$agence) {
                $agence = new Agence();
                $agence->setCode('01');
                $agence->setNom('Agence de Test');
                $this->em->persist($agence);
                $output->writeln('   ✓ Agence créée: 01 - Agence de Test');
            } else {
                $output->writeln('   ✓ Agence existe déjà: ' . $agence->getCode() . ' - ' . $agence->getNom());
            }

            // 2. Créer un service de test
            $output->writeln('2. Création du service de test...');
            $service = $this->em->getRepository(\App\Entity\Admin\AgenceService\Service::class)->findOneBy(['code' => 'SERV01']);
            if (!$service) {
                $service = new \App\Entity\Admin\AgenceService\Service();
                $service->setCode('SERV01');
                $service->setNom('Service de Test');
                $this->em->persist($service);
                $output->writeln('   ✓ Service créé: SERV01 - Service de Test');
            } else {
                $output->writeln('   ✓ Service existe déjà: ' . $service->getCode() . ' - ' . $service->getNom());
            }

            // 3. Créer un AgenceServiceIrium
            $output->writeln('3. Création de l\'AgenceServiceIrium...');
            $agenceService = $this->em->getRepository(AgenceServiceIrium::class)->findOneBy(['agence' => $agence, 'service' => $service]);
            if (!$agenceService) {
                $agenceService = new AgenceServiceIrium();
                $agenceService->setAgence($agence);
                $agenceService->setService($service);
                $this->em->persist($agenceService);
                $output->writeln('   ✓ AgenceServiceIrium créé');
            } else {
                $output->writeln('   ✓ AgenceServiceIrium existe déjà');
            }

            // 4. Créer un personnel de test
            $output->writeln('4. Création du personnel de test...');
            $personnel = $this->em->getRepository(Personnel::class)->findOneBy(['matricule' => 12345]);
            if (!$personnel) {
                $personnel = new Personnel();
                $personnel->setNom('Test');
                $personnel->setPrenoms('Utilisateur');
                $personnel->setMatricule(12345);
                $personnel->setAgenceServiceIrium($agenceService);
                $this->em->persist($personnel);
                $output->writeln('   ✓ Personnel créé: Test Utilisateur (12345)');
            } else {
                $output->writeln('   ✓ Personnel existe déjà: ' . $personnel->getNom() . ' ' . $personnel->getPrenoms());
            }

            // 5. Créer un utilisateur de test
            $output->writeln('5. Création de l\'utilisateur de test...');
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => 'testuser']);
            if (!$user) {
                $user = new User();
                $user->setUsername('testuser');
                $user->setEmail('test@example.com');
                $user->setFullname('Test Utilisateur');
                $user->setMatricule('12345');
                $user->setRoles(['ROLE_USER']);
                $user->setPersonnel($personnel);

                $this->em->persist($user);
                $output->writeln('   ✓ Utilisateur créé: testuser (mot de passe: password)');
            } else {
                $output->writeln('   ✓ Utilisateur existe déjà: ' . $user->getUsername());
            }

            // Sauvegarder en base
            $this->em->flush();

            $output->writeln('');
            $output->writeln('🎉 Utilisateur de test créé avec succès !');
            $output->writeln('Vous pouvez maintenant vous connecter avec:');
            $output->writeln('  - Nom d\'utilisateur: testuser');
            $output->writeln('  - Pas de mot de passe requis (authentification LDAP)');
            $output->writeln('  - Agence: 01 - Agence de Test');
            $output->writeln('  - Service: SERV01 - Service de Test');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('❌ Erreur: ' . $e->getMessage());
            $output->writeln('Stack trace:');
            $output->writeln($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
