<?php

namespace App\DataFixtures\dom;

use App\Entity\Dom\DomSousTypeDocument;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SousTypeDocumentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sousTypes = [
            ['code' => 'MISSION', 'reference' => 'sous_type_mission'],
            ['code' => 'COMPLEMENT', 'reference' => 'sous_type_complement'],
            ['code' => 'MUTATION', 'reference' => 'sous_type_mutation'],
            ['code' => 'FRAIS EXCEPTIONNEL', 'reference' => 'sous_type_frais_exceptionnel'],
            ['code' => 'TROP PERCU', 'reference' => 'sous_type_trop_percu'],
            // Ajoutez d'autres sous-types ici si nécessaire
        ];

        foreach ($sousTypes as $sousTypeData) {
            $sousType = new DomSousTypeDocument();
            $sousType->setCodeSousType($sousTypeData['code']);

            $manager->persist($sousType);
            $this->addReference($sousTypeData['reference'], $sousType);
        }

        $manager->flush();
    }
}
