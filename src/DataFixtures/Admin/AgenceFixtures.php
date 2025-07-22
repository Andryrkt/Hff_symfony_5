<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\AgenceService\Agence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AgenceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Agence antananarivo
        $antanarivo = new Agence();
        $antanarivo->setCode('01');
        $antanarivo->setNom('ANTANANARIVO');
        $manager->persist($antanarivo);

        // Agence Cessna ivato
        $cessnaIvato = new Agence();
        $cessnaIvato->setCode('02');
        $cessnaIvato->setNom('CESSNA IVATO');
        $manager->persist($cessnaIvato);

        // Agence Fort-Dauphin
        $fortDauphin = new Agence();
        $fortDauphin->setCode('20');
        $fortDauphin->setNom('FORT-DAUPHINE');
        $manager->persist($fortDauphin);

        // Agence Ambatovy
        $ambatovy = new Agence();
        $ambatovy->setCode('30');
        $ambatovy->setNom('AMBATOVY');
        $manager->persist($ambatovy);

        // Agence tamatave
        $tamatave = new Agence();
        $tamatave->setCode('40');
        $tamatave->setNom('TAMATAVE');
        $manager->persist($tamatave);

        // Agence rental
        $rental = new Agence();
        $rental->setCode('50');
        $rental->setNom('RENTAL');
        $manager->persist($rental);

        // Agence rental
        $rental = new Agence();
        $rental->setCode('50');
        $rental->setNom('RENTAL');
        $manager->persist($rental);

        // Agence pneu outil lub
        $pneuOutilLub = new Agence();
        $pneuOutilLub->setCode('60');
        $pneuOutilLub->setNom('PNEU - OUTIL - LUB');
        $manager->persist($pneuOutilLub);

        // Agence administration
        $administration = new Agence();
        $administration->setCode('80');
        $administration->setNom('ADMINISTRATION');
        $manager->persist($administration);

        // Agence comm energie
        $commEnergie = new Agence();
        $commEnergie->setCode('90');
        $commEnergie->setNom('COMM ENERGIE');
        $manager->persist($commEnergie);

        // Agence energie durable
        $energieDurable = new Agence();
        $energieDurable->setCode('91');
        $energieDurable->setNom('ENERGIE DURABLE');
        $manager->persist($energieDurable);

        // Agence energie jirama
        $energieJirama = new Agence();
        $energieJirama->setCode('92');
        $energieJirama->setNom('ENERGIE JIRAMA');
        $manager->persist($energieJirama);

        // Agence travel airways
        $travelAirways = new Agence();
        $travelAirways->setCode('C1');
        $travelAirways->setNom('TRAVEL AIRWAYS');
        $manager->persist($travelAirways);

        $manager->flush();

        $this->addReference('agence_antanarivo', $antanarivo);
        $this->addReference('agence_cessna_ivato', $cessnaIvato);
        $this->addReference('agence_fort_dauphin', $fortDauphin);
        $this->addReference('agence_ambatovy', $ambatovy);
        $this->addReference('agence_tamatave', $tamatave);
        $this->addReference('agence_rental', $rental);
        $this->addReference('agence_pneu_outil_lub', $pneuOutilLub);
        $this->addReference('agence_administration', $administration);
        $this->addReference('agence_comm_energie', $commEnergie);
        $this->addReference('agence_energie_durable', $energieDurable);
        $this->addReference('agence_energie_jirama', $energieJirama);
        $this->addReference('agence_travel_airways', $travelAirways);
    }
}
