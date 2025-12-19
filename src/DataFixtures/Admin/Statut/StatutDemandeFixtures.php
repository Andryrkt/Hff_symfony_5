<?php

namespace App\DataFixtures\Admin\Statut;

use App\Entity\Admin\Statut\StatutDemande;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatutDemandeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuts = [
            /** =========== Statuts DOM ===============*/
            // Statuts OUVERT
            ['app' => 'DOM', 'code' => 'OUV', 'description' => 'OUVERT'],
            ['app' => 'DOM', 'code' => 'OUV', 'description' => 'ATTENTE PAIEMENT'],
            ['app' => 'DOM', 'code' => 'OUV', 'description' => 'CONTROLE SERVICE'],
            ['app' => 'DOM', 'code' => 'OUV', 'description' => 'VALIDATION DG'],
            ['app' => 'DOM', 'code' => 'OUV', 'description' => 'VALIDATION RH'],
            ['app' => 'DOM', 'code' => 'OUV', 'description' => 'VALIDE COMPTABILITE'],
            ['app' => 'DOM', 'code' => 'OUV', 'description' => 'VALIDE'],
            ['app' => 'DOM', 'code' => 'OUV', 'description' => 'PRE-CONTROLE ATELIER'],
            ['app' => 'DOM', 'code' => 'OUV', 'description' => 'VALIDATION COMPTA'],

            // Statuts ENCOURS
            ['app' => 'DOM', 'code' => 'ENC', 'description' => 'ENCOURS'],

            // Statuts CLOTURE
            ['app' => 'DOM', 'code' => 'CLO', 'description' => 'CLOTURE'],

            // Statuts COMPTA
            ['app' => 'DOM', 'code' => 'CPT', 'description' => 'COMPTA'],

            // Statuts PAYE
            ['app' => 'DOM', 'code' => 'PAY', 'description' => 'PAYE'],

            // Statuts ANNULE
            ['app' => 'DOM', 'code' => 'ANN', 'description' => 'ANNULE'],
            ['app' => 'DOM', 'code' => 'ANN', 'description' => 'ANNULE CHEF DE SERVICE'],
            ['app' => 'DOM', 'code' => 'ANN', 'description' => 'ANNULE COMPTABILITE'],
            ['app' => 'DOM', 'code' => 'ANN', 'description' => 'ANNULE SECRETARIAT RH'],
            ['app' => 'DOM', 'code' => 'ANN', 'description' => 'ANNULE RH'],

            /** =========== Statuts CAS ===============*/
            ['app' => 'CAS', 'code' => 'ATV', 'description' => 'ATTENTE VALIDATION'],
            ['app' => 'CAS', 'code' => 'VAL', 'description' => 'VALIDER']

            /** =========== Statuts BADM ===============*/

            /** =========== Statuts DIT ===============*/
        ];

        foreach ($statuts as $statutData) {
            $statut = new StatutDemande();
            $statut->setCodeApplication($statutData['app'])
                ->setCodeStatut($statutData['code'])
                ->setDescription($statutData['description']);

            $manager->persist($statut);

            // Création d'une référence pour chaque statut
            $referenceKey = sprintf(
                '%s_statut_%s_%s',
                strtolower($statutData['app']),
                strtolower($statutData['code']),
                strtolower(str_replace(' ', '_', $statutData['description']))
            );
            $this->addReference($referenceKey, $statut);
        }

        $manager->flush();
    }
}
