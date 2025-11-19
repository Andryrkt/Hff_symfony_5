<?php

namespace App\DataFixtures\Test;



use Doctrine\Persistence\ObjectManager;
use App\Entity\Admin\PersonnelUser\User;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TestUserFixtures extends Fixture
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
        $user = new User();
        $user->setEmail('test.user@hff.mg');
        $user->setRoles(['ROLE_USER', 'RH_ORDRE_MISSION_CREATE']);
        
        $users = [
            ['username' => 'test', 'fullname' => 'Utilisateur Test', 'matricule' => '9999', 'email' => 'test.user@hff.mg', 'roles' => ['ROLE_USER'], 'personnel_ref' => 'personnel_p1', 'reference' => 'user_u1', 'permissions' => ['permission_RH_ORDRE_MISSION_CREATE', 'permission_RH_ORDRE_MISSION_VIEW']],
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setFullname($userData['fullname']);
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setMatricule($userData['matricule']);
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
