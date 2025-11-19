<?php

namespace App\DataFixtures\Test;

use App\Entity\Admin\AgenceService\Agence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TestAgenceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $agences = [
            ['code' => '01', 'nom' => 'ANTANANARIVO', 'reference' => 'agence_antanarivo_test'],
            ['code' => '02', 'nom' => 'CESSNA IVATO', 'reference' => 'agence_cessna_ivato_test'],
            ['code' => '20', 'nom' => 'FORT-DAUPHIN', 'reference' => 'agence_fort_dauphin_test'],
            ['code' => '30', 'nom' => 'AMBATOVY', 'reference' => 'agence_ambatovy_test'],
            ['code' => '40', 'nom' => 'TAMATAVE', 'reference' => 'agence_tamatave_test'],
            ['code' => '50', 'nom' => 'RENTAL', 'reference' => 'agence_rental_test'],
            ['code' => '60', 'nom' => 'PNEU - OUTIL - LUB', 'reference' => 'agence_pneu_outil_lub_test'],
            ['code' => '80', 'nom' => 'ADMINISTRATION', 'reference' => 'agence_administration_test'],
            ['code' => '90', 'nom' => 'COMM ENERGIE', 'reference' => 'agence_comm_energie_test'],
            ['code' => '91', 'nom' => 'ENERGIE DURABLE', 'reference' => 'agence_energie_durable_test'],
            ['code' => '92', 'nom' => 'ENERGIE JIRAMA', 'reference' => 'agence_energie_jirama_test'],
            ['code' => 'C1', 'nom' => 'TRAVEL AIRWAYS', 'reference' => 'agence_travel_airways_test'],
        ];

        foreach ($agences as $agenceData) {
            $agence = new Agence();
            $agence->setCode($agenceData['code'])
                ->setNom($agenceData['nom']);

            $manager->persist($agence);
            $this->addReference($agenceData['reference'], $agence);
        }

        $manager->flush();
    }
}
