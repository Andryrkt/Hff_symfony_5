<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\Admin\UserFixtures;
use App\DataFixtures\Admin\AgenceFixtures;
use App\DataFixtures\Admin\ServiceFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\DataFixtures\Admin\ApplicationFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserAccessFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $userAccess = new UserAccess();

        // Récupérer les références des entités liées
        // Assurez-vous que ces références sont définies dans d'autres fixtures
        $user = $this->getReference('user_test');
        $agence = $this->getReference('agence_administration');
        $service = $this->getReference('service_inf');
        $application = $this->getReference('app_dom');

        $userAccess->setUsers($user);
        $userAccess->setAgence($agence);
        $userAccess->setService($service);
        $userAccess->setApplication($application);

        $manager->persist($userAccess);
        $manager->flush();

        // Vous pouvez ajouter une référence à cette fixture si d'autres en dépendent
        $this->addReference('user_access_test', $userAccess);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            AgenceFixtures::class,
            ServiceFixtures::class,
            ApplicationFixtures::class,
        ];
    }
}
