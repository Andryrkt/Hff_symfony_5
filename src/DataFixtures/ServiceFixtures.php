<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $neg = new Service();
        $neg->setCode('NEG');
        $neg->setNom('Magasin');
        $manager->persist($neg);
        $this->addReference('service_neg', $neg);

        $ate = new Service();
        $ate->setCode('ATE');
        $ate->setNom('Atelier');
        $manager->persist($ate);
        $this->addReference('service_ate', $ate);

        $manager->flush();
    }
}
