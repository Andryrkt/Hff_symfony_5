<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\PersonnelUser\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('test');
        $user->setFullname('Utilisateur Test');
        $user->setEmail('test@hff.mg');
        $user->setRoles(['ROLE_USER']);
        $user->addGroup($this->getReference('group_energie'));
        $user->setPersonnel($this->getReference('personnel_p1'));

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user_test', $user);
    }

    public function getDependencies(): array
    {
        return [
            PersonnelFixtures::class,
            GroupFixtures::class,
        ];
    }
}
