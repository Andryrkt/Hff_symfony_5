<?php

namespace App\DataFixtures\Admin\Statut;

use App\Entity\Admin\Statut\StatutDemande;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatutDemandeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Statuts DOM (Dossier)
        $statuts = [
            // Statuts OUVERT
            ['code' => 'OUV', 'description' => 'OUVERT'],
            ['code' => 'OUV', 'description' => 'ATTENTE PAIEMENT'],
            ['code' => 'OUV', 'description' => 'CONTROLE SERVICE'],
            ['code' => 'OUV', 'description' => 'VALIDATION DG'],
            ['code' => 'OUV', 'description' => 'VALIDATION RH'],
            ['code' => 'OUV', 'description' => 'VALIDE COMPTABILITE'],
            ['code' => 'OUV', 'description' => 'VALIDE'],
            ['code' => 'OUV', 'description' => 'PRE-CONTROLE ATELIER'],
            ['code' => 'OUV', 'description' => 'VALIDATION COMPTA'],

            // Statuts ENCOURS
            ['code' => 'ENC', 'description' => 'ENCOURS'],

            // Statuts CLOTURE
            ['code' => 'CLO', 'description' => 'CLOTURE'],

            // Statuts COMPTA
            ['code' => 'CPT', 'description' => 'COMPTA'],

            // Statuts PAYE
            ['code' => 'PAY', 'description' => 'PAYE'],

            // Statuts ANNULE
            ['code' => 'ANN', 'description' => 'ANNULE'],
            ['code' => 'ANN', 'description' => 'ANNULE CHEF DE SERVICE'],
            ['code' => 'ANN', 'description' => 'ANNULE COMPTABILITE'],
            ['code' => 'ANN', 'description' => 'ANNULE SECRETARIAT RH'],
            ['code' => 'ANN', 'description' => 'ANNULE RH'],
        ];

        foreach ($statuts as $statutData) {
            $statut = new StatutDemande();
            $statut->setCodeApplication('DOM')
                ->setCodeStatut($statutData['code'])
                ->setDescription($statutData['description']);

            $manager->persist($statut);

            // Création d'une référence pour chaque statut
            $referenceKey = sprintf(
                'dom_statut_%s_%s',
                strtolower($statutData['code']),
                strtolower(str_replace(' ', '_', $statutData['description']))
            );
            $this->addReference($referenceKey, $statut);
        }

        $manager->flush();
    }
}
