<?php

namespace App\DataFixtures;

use App\Entity\Personnel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PersonnelFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $p1 = new Personnel();
        $p1->setNom('Rabe');
        $p1->setPrenom('Jean');
        $p1->setAgenceService($this->getReference('agence_service_neg'));
        $manager->persist($p1);

        $manager->flush();

        $this->addReference('personnel_jean', $p1);
    }

    public function getDependencies(): array
    {
        return [
            AgenceServiceFixtures::class,
        ];
    }
}
