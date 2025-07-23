<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\PersonnelUser\Personnel;
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
        $p1->setAgenceServiceIrium($this->getReference('agence_service_administration_inf_DA14'));
        $manager->persist($p1);

        $manager->flush();

        $this->addReference('personnel_p1', $p1);
    }

    public function getDependencies(): array
    {
        return [
            AgenceServiceIriumFixtures::class,
            GroupFixtures::class,
        ];
    }
}
