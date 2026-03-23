<?php

namespace App\DataFixtures\Hf\Dit;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Hf\Atelier\Dit\WorNiveauUrgence;

class WorNiveauUrgenceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $worNiveauUrgences = [
            ['code' => 'P0'],
            ['code' => 'P1'],
            ['code' => 'P2'],
            ['code' => 'P3'],
        ];

        foreach ($worNiveauUrgences as $worNiveauUrgenceData) {
            $worNiveauUrgence = new WorNiveauUrgence();
            $worNiveauUrgence->setCode($worNiveauUrgenceData['code']);
            $manager->persist($worNiveauUrgence);
            $this->addReference('wor_niveau_urgence_' . $worNiveauUrgenceData['code'], $worNiveauUrgence);
        }
        $manager->flush();
    }
}
