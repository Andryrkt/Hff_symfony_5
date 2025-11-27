<?php


namespace App\DataFixtures\Admin\PersonnelUser;

use App\DataFixtures\Admin\ApplicationGroupe\PermissionFixtures;
use App\Entity\Admin\PersonnelUser\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['prod'];
    }
    public function load(ObjectManager $manager): void
    {
        // $users = [
        //     ['username' => 'test', 'fullname' => 'Utilisateur Test', 'matricule' => '9999', 'email' => 'test@hff.mg', 'roles' => ['ROLE_USER'], 'personnel_ref' => 'personnel_p1', 'reference' => 'user_u1', 'permissions' => ['permission_RH_ORDRE_MISSION_CREATE', 'permission_RH_ORDRE_MISSION_VIEW']],
        //     ['username' => 'lanto', 'fullname' => 'Lanto Andrianadison', 'matricule' => '9998', 'email' => 'lanto@hff.mg', 'roles' => ['ROLE_USER'], 'personnel_ref' => 'personnel_p2', 'reference' => 'user_u2', 'permissions' => ['permission_RH_ORDRE_MISSION_CREATE', 'permission_RH_ORDRE_MISSION_VIEW']]
        // ];

        // foreach ($users as $userData) {
        //     $user = new User();
        //     $user->setUsername($userData['username']);
        //     $user->setFullname($userData['fullname']);
        //     $user->setEmail($userData['email']);
        //     $user->setRoles($userData['roles']);
        //     $user->setMatricule($userData['matricule']);
        //     $user->setPersonnel($this->getReference($userData['personnel_ref']));

        //     // Ajout des permissions directes
        //     foreach ($userData['permissions'] as $permissionRef) {
        //         $permission = $this->getReference($permissionRef);
        //         $user->addPermissionsDirecte($permission);
        //     }

        //     $manager->persist($user);
        //     $this->addReference($userData['reference'], $user);
        // }

        // $manager->flush();
    }

    public function getDependencies()
    {
        // return [
        //     PersonnelFixtures::class,
        //     PermissionFixtures::class
        // ];
    }
}
