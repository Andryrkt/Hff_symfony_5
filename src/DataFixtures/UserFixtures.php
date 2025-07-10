<?php

namespace App\DataFixtures;

use App\Entity\Admin\PersonnelUser\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{


    public function load(ObjectManager $manager): void
    {
        // Créer un utilisateur de test
        $user = new User();
        $user->setUsername('test');
        $user->setFullname('Utilisateur Test');
        $user->setEmail('test@hff.mg');
        $user->setRoles(['ROLE_USER']);
        $user->addGroup($this->getReference('group_energie'));
        $user->setPersonnel($this->getReference('personnel_jean'));

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user_test', $user);
    }
}
