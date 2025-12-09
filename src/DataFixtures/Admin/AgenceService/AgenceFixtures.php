<?php

namespace App\DataFixtures\Admin\AgenceService;

use App\Entity\Admin\AgenceService\Agence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AgenceFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['prod'];
    }
    public function load(ObjectManager $manager): void
    {
        $agences = [
            ['code' => '01', 'nom' => 'ANTANANARIVO', 'reference' => 'agence_antanarivo'],
            ['code' => '02', 'nom' => 'CESSNA IVATO', 'reference' => 'agence_cessna_ivato'],
            ['code' => '20', 'nom' => 'FORT-DAUPHIN', 'reference' => 'agence_fort_dauphin'],
            ['code' => '30', 'nom' => 'AMBATOVY', 'reference' => 'agence_ambatovy'],
            ['code' => '40', 'nom' => 'TAMATAVE', 'reference' => 'agence_tamatave'],
            ['code' => '50', 'nom' => 'RENTAL', 'reference' => 'agence_rental'],
            ['code' => '60', 'nom' => 'PNEU - OUTIL - LUB', 'reference' => 'agence_pneu_outil_lub'],
            ['code' => '80', 'nom' => 'ADMINISTRATION', 'reference' => 'agence_administration'],
            ['code' => '90', 'nom' => 'COMM ENERGIE', 'reference' => 'agence_comm_energie'],
            ['code' => '91', 'nom' => 'ENERGIE DURABLE', 'reference' => 'agence_energie_durable'],
            ['code' => '92', 'nom' => 'ENERGIE JIRAMA', 'reference' => 'agence_energie_jirama'],
            ['code' => 'C1', 'nom' => 'TRAVEL AIRWAYS', 'reference' => 'agence_travel_airways'],
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
