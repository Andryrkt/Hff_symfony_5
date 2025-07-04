<?php

namespace App\DataFixtures;

use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AgenceServiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $asNeg = new AgenceServiceIrium();
        $asNeg->setAgence($this->getReference('agence_antanarivo'));
        $asNeg->setService($this->getReference('service_irum'));
        $asNeg->setCode('01NEG');
        $asNeg->setResponsable('Prisca');
        $manager->persist($asNeg);
        $this->addReference('agence_service_neg', $asNeg);

        $asAte = new AgenceServiceIrium();
        $asAte->setAgence($this->getReference('agence_tana'));
        $asAte->setService($this->getReference('service_ate'));
        $asAte->setCode('01ATE');
        $asAte->setResponsable('Jaona');
        $manager->persist($asAte);
        $this->addReference('agence_service_ate', $asAte);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AgenceFixtures::class,
            ServiceFixtures::class,
        ];
    }
}
