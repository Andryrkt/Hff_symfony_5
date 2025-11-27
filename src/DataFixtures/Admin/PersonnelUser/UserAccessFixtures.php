<?php

namespace App\DataFixtures\Admin\PersonnelUser;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\DataFixtures\Admin\AgenceService\AgenceFixtures;
use App\DataFixtures\Admin\AgenceService\ServiceFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\Admin\ApplicationGroupe\PermissionFixtures;

class UserAccessFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // $userAccesss = [
        //     //pour user test
        //     ['user' => 'user_u1', 'agence' => 'agence_administration', 'service' => 'service_inf', 'allAgence' => false, 'allService' => false, 'permissions' => ['permission_RH_ORDRE_MISSION_CREATE', 'permission_RH_ORDRE_MISSION_VIEW']],
        //     // pour user lanto
        //     ['user' => 'user_u2', 'agence' => null, 'service' => null, 'allAgence' => true, 'allService' => true, 'permissions' => ['permission_RH_ORDRE_MISSION_CREATE', 'permission_RH_ORDRE_MISSION_VIEW']],
        // ];


        // foreach ($userAccesss as $userAccessData) {
        //     $userAccess = new UserAccess();

        //     $userAccess->setUsers($this->getReference($userAccessData['user']));
        //     if ($userAccessData['agence'] !== null) {
        //         $userAccess->setAgence($this->getReference($userAccessData['agence']));
        //     } else {
        //         $userAccess->setAgence(null);
        //     }
        //     if ($userAccessData['service'] !== null) {
        //         $userAccess->setService($this->getReference($userAccessData['service']));
        //     } else {
        //         $userAccess->setService(null);
        //     }
        //     $userAccess->setAllAgence($userAccessData['allAgence']);
        //     $userAccess->setAllService($userAccessData['allService']);

        //     foreach ($userAccessData['permissions'] as $permissionRef) {
        //         $permission = $this->getReference($permissionRef);
        //         $userAccess->addPermission($permission);
        //     }

        //     $manager->persist($userAccess);
        // }

        // $manager->flush();
    }

    public function getDependencies()
    {
        // return [
        //     UserFixtures::class,
        //     AgenceFixtures::class,
        //     ServiceFixtures::class,
        //     PermissionFixtures::class
        // ];
    }
}
