<?php

namespace App\DataFixtures\Admin\AgenceService;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AgenceServiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Définir les associations agence-service
        $associations = [
            // Antananarivo 01
            'agence_antanarivo' => ['NEG', 'COM', 'ATE', 'CSP', 'GAR', 'ASS', 'FLE', 'MAS', 'MAP'],

            // Cessna Ivato 02
            'agence_cessna_ivato' => ['NEG', 'ATE', 'LCD'],

            // Fort-Dauphin 20
            'agence_fort_dauphin' => ['NEG', 'ATE', 'MAP'],

            // Ambatovy 30
            'agence_ambatovy' => ['NEG', 'ATE', 'MAN', 'FLE'],

            // Tamatave 40
            'agence_tamatave' => ['NEG', 'ATE', 'LCD', 'FLE', 'LEV'],

            // Rental 50
            'agence_rental' => ['LCD', 'LTV', 'LFD', 'LBV', 'LR6', 'LST', 'LSC'],

            // pneu-outil-lub 60
            'agence_pneu_outil_lub' => ['NEG', 'ATE', 'MAP'],

            // Administration 80
            'agence_administration' => ['DIR', 'FIN', 'PER', 'INF', 'IMM', 'TRA', 'APP', 'UMP'],

            // com energie 90
            'agence_comm_energie' => ['COM', 'LGR'],

            // energie durable 91
            'agence_energie_durable' => ['VAT', 'BLK', 'ENG', 'SLR'],

            //energie jirama 92
            'agence_energie_jirama' => ['MAH', 'NOS', 'TUL', 'AMB', 'LCJ', 'TSI'],

            // travel airways c1
            'agence_travel_airways' => ['C1']

            // Ajoutez les autres associations selon vos besoins...
        ];

        foreach ($associations as $agenceRef => $serviceCodes) {
            $agence = $this->getReference($agenceRef);

            foreach ($serviceCodes as $serviceCode) {
                $service = $this->getReference('service_' . strtolower($serviceCode));
                $agence->addService($service); // Utilisez la méthode appropriée de votre entité Agence
            }

            $manager->persist($agence);
        }

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
