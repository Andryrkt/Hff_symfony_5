<?php

namespace App\DataFixtures\Test;

use App\Entity\Admin\PersonnelUser\Personnel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TestPersonnelFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            TestAgenceServiceIriumFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $personnels = [
            ['nom' => 'TEST', 'prenoms' => 'TEST', 'matricule' => '9999', 'codeBancaire' => null, 'agServIrium' => 'agence_service_administration_inf_DA14', 'reference' => 'personnel_p1'],
            
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

    
}
