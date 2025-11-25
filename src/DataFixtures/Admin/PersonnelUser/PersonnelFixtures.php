<?php

namespace App\DataFixtures\Admin\PersonnelUser;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Admin\PersonnelUser\Personnel;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\Admin\AgenceService\AgenceServiceIriumFixtures;

class PersonnelFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['prod'];
    }
    public function load(ObjectManager $manager): void
    {
        $personnels = [
            ['nom' => 'Rabe', 'prenoms' => 'Jean', 'matricule' => '9999', 'codeBancaire' => null, 'agServIrium' => 'agence_service_administration_inf_DA14', 'reference' => 'personnel_p1'],
            ['nom' => 'Rakoto', 'prenoms' => 'Doe', 'matricule' => '9998', 'codeBancaire' => '4875 96321547 89966 3211 4778', 'agServIrium' => 'agence_service_administration_inf_DA14', 'reference' => 'personnel_p2']
        ];


        foreach ($personnels as $personelData) {
            $personnel = new Personnel();
            $personnel->setNom($personelData['nom']);
            $personnel->setPrenoms($personelData['prenoms']);
            $personnel->setMatricule($personelData['matricule']);
            $personnel->setNumeroCompteBancaire($personelData['codeBancaire']);
            $personnel->setAgenceServiceIrium($this->getReference($personelData['agServIrium']));

            $manager->persist($personnel);
            $this->addReference($personelData['reference'], $personnel);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AgenceServiceIriumFixtures::class
        ];
    }
}
