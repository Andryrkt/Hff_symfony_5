<?php

namespace App\DataFixtures\Test;

use App\Entity\Admin\PersonnelUser\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestUserFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            TestPersonnelFixtures::class,
            TestPermissionFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'username' => 'test',
                'fullname' => 'Utilisateur Test',
                'matricule' => '9999',
                'email' => 'test.user@hff.mg',
                'roles' => ['ROLE_USER'],
                'personnel_ref' => 'personnel_p1',
                'reference' => 'user_u1',
                'permissions' => ['permission_RH_ORDRE_MISSION_CREATE', 'permission_RH_ORDRE_MISSION_VIEW']
            ],
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setFullname($userData['fullname']);
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setMatricule($userData['matricule']);


            // Associe le personnel
            $user->setPersonnel($this->getReference($userData['personnel_ref']));

            // Ajout des permissions directes
            foreach ($userData['permissions'] as $permissionRef) {
                $permission = $this->getReference($permissionRef);
                $user->addPermissionsDirecte($permission);
            }

            $manager->persist($user);
            $this->addReference($userData['reference'], $user);
        }

        $manager->flush();
    }
}
