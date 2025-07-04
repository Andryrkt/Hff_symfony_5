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
        //tana - neg 1
        $asNeg1 = new AgenceServiceIrium();
        $asNeg1->setAgence($this->getReference('agence_antanarivo'));
        $asNeg1->setService($this->getReference('service_neg'));
        $asNeg1->setCode('01-NEG');
        $asNeg1->setSociete('HF');
        $asNeg1->setCodeSage('AB11');
        $asNeg1->setResponsable('Prisca');
        $manager->persist($asNeg1);
        $this->addReference('agence_service_neg1', $asNeg1);

        //tana - neg 2
        $asNeg2 = new AgenceServiceIrium();
        $asNeg2->setAgence($this->getReference('agence_antanarivo'));
        $asNeg2->setService($this->getReference('service_neg'));
        $asNeg2->setCode('01-NEG');
        $asNeg2->setSociete('HF');
        $asNeg2->setCodeSage('AB21');
        $asNeg2->setResponsable('Prisca');
        $manager->persist($asNeg2);
        $this->addReference('agence_service_neg2', $asNeg2);

        //tana - com
        $asCom = new AgenceServiceIrium();
        $asCom->setAgence($this->getReference('agence_antanarivo'));
        $asCom->setService($this->getReference('service_com'));
        $asCom->setCode('01-COM');
        $asCom->setSociete('HF');
        $asCom->setCodeSage('AB51');
        $asCom->setResponsable('Paul');
        $manager->persist($asCom);
        $this->addReference('agence_service_com', $asCom);

        //tana - ate
        $asAte = new AgenceServiceIrium();
        $asAte->setAgence($this->getReference('agence_antanarivo'));
        $asAte->setService($this->getReference('service_ate'));
        $asAte->setCode('01-ATE');
        $asAte->setSociete('HF');
        $asAte->setCodeSage('AC11');
        $asAte->setResponsable('Jaona');
        $manager->persist($asAte);
        $this->addReference('agence_service_ate', $asAte);

        //tana - csp
        $asCsp = new AgenceServiceIrium();
        $asCsp->setAgence($this->getReference('agence_antanarivo'));
        $asCsp->setService($this->getReference('service_csp'));
        $asCsp->setCode('01-CSP');
        $asCsp->setSociete('HF');
        $asCsp->setCodeSage('AC12');
        $asCsp->setResponsable('Jaona');
        $manager->persist($asCsp);
        $this->addReference('agence_service_csp', $asCsp);

        //tana - gar
        $asGar = new AgenceServiceIrium();
        $asGar->setAgence($this->getReference('agence_antanarivo'));
        $asGar->setService($this->getReference('service_gar'));
        $asGar->setCode('01-GAR');
        $asGar->setSociete('HF');
        $asGar->setCodeSage('AC14');
        $asGar->setResponsable('Jaona');
        $manager->persist($asGar);
        $this->addReference('agence_service_gar', $asGar);

        //tana - for
        $asFor = new AgenceServiceIrium();
        $asFor->setAgence($this->getReference('agence_antanarivo'));
        $asFor->setService($this->getReference('service_for'));
        $asFor->setCode('01-FOR');
        $asFor->setSociete('HF');
        $asFor->setCodeSage('AC16');
        $asFor->setResponsable('Jaona');
        $manager->persist($asFor);
        $this->addReference('agence_service_for', $asFor);

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
