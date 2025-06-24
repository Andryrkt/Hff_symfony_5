<?php

namespace App\DataFixtures;

use App\Entity\Agence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AgenceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tana = new Agence();
        $tana->setCode('01');
        $tana->setNom('ANTANANARIVO');
        $manager->persist($tana);

        $manager->flush();

        $this->addReference('agence_tana', $tana);
    }
}
