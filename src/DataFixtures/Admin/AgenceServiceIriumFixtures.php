<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AgenceServiceIriumFixtures extends Fixture implements DependentFixtureInterface
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
        $this->addReference('agence_service_tananeg1', $asNeg1);

        //tana - neg 2
        $asNeg2 = new AgenceServiceIrium();
        $asNeg2->setAgence($this->getReference('agence_antanarivo'));
        $asNeg2->setService($this->getReference('service_neg'));
        $asNeg2->setCode('01-NEG');
        $asNeg2->setSociete('HF');
        $asNeg2->setCodeSage('AB21');
        $asNeg2->setResponsable('Prisca');
        $manager->persist($asNeg2);
        $this->addReference('agence_service_tana_neg2', $asNeg2);

        //tana - com
        $asCom = new AgenceServiceIrium();
        $asCom->setAgence($this->getReference('agence_antanarivo'));
        $asCom->setService($this->getReference('service_com'));
        $asCom->setCode('01-COM');
        $asCom->setSociete('HF');
        $asCom->setCodeSage('AB51');
        $asCom->setResponsable('Paul');
        $manager->persist($asCom);
        $this->addReference('agence_service_tanacom', $asCom);

        //tana - ate
        $asAte = new AgenceServiceIrium();
        $asAte->setAgence($this->getReference('agence_antanarivo'));
        $asAte->setService($this->getReference('service_ate'));
        $asAte->setCode('01-ATE');
        $asAte->setSociete('HF');
        $asAte->setCodeSage('AC11');
        $asAte->setResponsable('Jaona');
        $manager->persist($asAte);
        $this->addReference('agence_service_tana_ate', $asAte);

        //tana - csp
        $asCsp = new AgenceServiceIrium();
        $asCsp->setAgence($this->getReference('agence_antanarivo'));
        $asCsp->setService($this->getReference('service_csp'));
        $asCsp->setCode('01-CSP');
        $asCsp->setSociete('HF');
        $asCsp->setCodeSage('AC12');
        $asCsp->setResponsable('Jaona');
        $manager->persist($asCsp);
        $this->addReference('agence_service_tana_csp', $asCsp);

        //tana - gar
        $asGar = new AgenceServiceIrium();
        $asGar->setAgence($this->getReference('agence_antanarivo'));
        $asGar->setService($this->getReference('service_gar'));
        $asGar->setCode('01-GAR');
        $asGar->setSociete('HF');
        $asGar->setCodeSage('AC14');
        $asGar->setResponsable('Jaona');
        $manager->persist($asGar);
        $this->addReference('agence_service_tana_gar', $asGar);

        //tana - for
        $asFor = new AgenceServiceIrium();
        $asFor->setAgence($this->getReference('agence_antanarivo'));
        $asFor->setService($this->getReference('service_for'));
        $asFor->setCode('01-FOR');
        $asFor->setSociete('HF');
        $asFor->setCodeSage('AC16');
        $asFor->setResponsable('');
        $manager->persist($asFor);
        $this->addReference('agence_service_tana_for', $asFor);

        //tana - ass
        $asAss = new AgenceServiceIrium();
        $asAss->setAgence($this->getReference('agence_antanarivo'));
        $asAss->setService($this->getReference('service_ass'));
        $asAss->setCode('01-ASS');
        $asAss->setSociete('HF');
        $asAss->setCodeSage('AG11');
        $asAss->setResponsable('Olivier');
        $manager->persist($asAss);
        $this->addReference('agence_service_tana_ass', $asAss);

        //Ambatovy - neg
        $asNeg = new AgenceServiceIrium();
        $asNeg->setAgence($this->getReference('agence_ambatovy'));
        $asNeg->setService($this->getReference('service_neg'));
        $asNeg->setCode('30-NEG');
        $asNeg->setSociete('HF');
        $asNeg->setCodeSage('BB21');
        $asNeg->setResponsable('Prisca');
        $manager->persist($asNeg);
        $this->addReference('agence_service_ambatovy_neg', $asNeg);

        //Ambatovy - ate
        $asAte = new AgenceServiceIrium();
        $asAte->setAgence($this->getReference('agence_ambatovy'));
        $asAte->setService($this->getReference('service_ate'));
        $asAte->setCode('30-ATE');
        $asAte->setSociete('HF');
        $asAte->setCodeSage('BC11');
        $asAte->setResponsable('Njara');
        $manager->persist($asAte);
        $this->addReference('agence_service_ambatovy_ate', $asAte);

        //Ambatovy - man
        $asMan = new AgenceServiceIrium();
        $asMan->setAgence($this->getReference('agence_ambatovy'));
        $asMan->setService($this->getReference('service_man'));
        $asMan->setCode('30-MAN');
        $asMan->setSociete('HF');
        $asMan->setCodeSage('BC15');
        $asMan->setResponsable('Njara');
        $manager->persist($asMan);
        $this->addReference('agence_service_ambatovy_man', $asMan);

        //cessna Ivato- neg
        $asNeg = new AgenceServiceIrium();
        $asNeg->setAgence($this->getReference('agence_cessna_ivato'));
        $asNeg->setService($this->getReference('service_neg'));
        $asNeg->setCode('02-NEG');
        $asNeg->setSociete('HF');
        $asNeg->setCodeSage('CB21');
        $asNeg->setResponsable('Daniel');
        $manager->persist($asNeg);
        $this->addReference('agence_service_cessna_ivato_neg', $asNeg);


        //cessna Ivato- ate
        $asAte = new AgenceServiceIrium();
        $asAte->setAgence($this->getReference('agence_cessna_ivato'));
        $asAte->setService($this->getReference('service_ate'));
        $asAte->setCode('02-ATE');
        $asAte->setSociete('HF');
        $asAte->setCodeSage('CC11');
        $asAte->setResponsable('Daniel');
        $manager->persist($asAte);
        $this->addReference('agence_service_cessna_ivato_ate', $asAte);

        //cessna Ivato- lcd
        $asLcd = new AgenceServiceIrium();
        $asLcd->setAgence($this->getReference('agence_cessna_ivato'));
        $asLcd->setService($this->getReference('service_lcd'));
        $asLcd->setCode('02-LCD');
        $asLcd->setSociete('HF');
        $asLcd->setCodeSage('CC121');
        $asLcd->setResponsable('Daniel');
        $manager->persist($asLcd);
        $this->addReference('agence_service_cessna_ivato_lcd', $asLcd);

        //administration - dir
        $asDir = new AgenceServiceIrium();
        $asDir->setAgence($this->getReference('agence_administration'));
        $asDir->setService($this->getReference('service_dir'));
        $asDir->setCode('80-DIR');
        $asDir->setSociete('HF');
        $asDir->setCodeSage('DA11');
        $asDir->setResponsable('Charles');
        $manager->persist($asDir);
        $this->addReference('agence_service_administration_dir', $asDir);

        //administration - fin
        $asFin = new AgenceServiceIrium();
        $asFin->setAgence($this->getReference('agence_administration'));
        $asFin->setService($this->getReference('service_fin'));
        $asFin->setCode('80-FIN');
        $asFin->setSociete('HF');
        $asFin->setCodeSage('DA12');
        $asFin->setResponsable('Patrick');
        $manager->persist($asFin);
        $this->addReference('agence_service_administration_fin', $asFin);

        //administration - per
        $asPer = new AgenceServiceIrium();
        $asPer->setAgence($this->getReference('agence_administration'));
        $asPer->setService($this->getReference('service_per'));
        $asPer->setCode('80-PER');
        $asPer->setSociete('HF');
        $asPer->setCodeSage('DA13');
        $asPer->setResponsable('tahina');
        $manager->persist($asPer);
        $this->addReference('agence_service_administration_per', $asPer);

        //administration - inf
        $asInf = new AgenceServiceIrium();
        $asInf->setAgence($this->getReference('agence_administration'));
        $asInf->setService($this->getReference('service_inf'));
        $asInf->setCode('80-INF');
        $asInf->setSociete('HF');
        $asInf->setCodeSage('DA14');
        $asInf->setResponsable('Olivier');
        $manager->persist($asInf);
        $this->addReference('agence_service_administration_inf', $asInf);


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
