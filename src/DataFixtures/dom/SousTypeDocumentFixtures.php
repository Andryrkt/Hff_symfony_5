<?php

namespace App\DataFixtures\dom;

use App\Entity\Dom\DomSousTypeDocument;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SousTypeDocumentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        //mission
        $sousTypeMission = new DomSousTypeDocument();
        $sousTypeMission->setCodeSousType('MISSION');
        $manager->persist($sousTypeMission);
        $this->addReference('sous_type_mission', $sousTypeMission);

        //complement
        $sousTypeComplement = new DomSousTypeDocument();
        $sousTypeComplement->setCodeSousType('COMPLEMENT');
        $manager->persist($sousTypeComplement);
        $this->addReference('sous_type_complement', $sousTypeComplement);


        //mutation
        $sousTypeMutation = new DomSousTypeDocument();
        $sousTypeMutation->setCodeSousType('MUTATION');
        $manager->persist($sousTypeMutation);
        $this->addReference('sous_type_mutation', $sousTypeMutation);

        //FRAIS EXCEPTIONNEL
        $sousTypeFraisExceptionnel = new DomSousTypeDocument();
        $sousTypeFraisExceptionnel->setCodeSousType('FRAIS EXCEPTIONNEL');
        $manager->persist($sousTypeFraisExceptionnel);
        $this->addReference('sous_type_frais_exceptionnel', $sousTypeFraisExceptionnel);

        //trop percu
        $sousTypeTropPercu = new DomSousTypeDocument();
        $sousTypeTropPercu->setCodeSousType('TROP PERCU');
        $manager->persist($sousTypeTropPercu);
        $this->addReference('sous_type_trop_percu', $sousTypeTropPercu);


        $manager->flush();
    }
}
