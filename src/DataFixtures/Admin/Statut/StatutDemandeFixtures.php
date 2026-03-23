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
            ['app' => 'CAS', 'code' => 'VAL', 'description' => 'VALIDER'],

            /** =========== Statuts BADM ===============*/
            ['app' => 'BDM', 'code' => 'OUV', 'description' => 'OUVERT'],
            ['app' => 'BDM', 'code' => 'ENC', 'description' => 'ENCOURS'],
            ['app' => 'BDM', 'code' => 'CLO', 'description' => 'CLOTURE'],
            ['app' => 'BDM', 'code' => 'ANN', 'description' => 'ANNULE'],
            ['app' => 'BDM', 'code' => 'OUV', 'description' => 'A VALIDER SERVICE EMETTEUR'],
            ['app' => 'BDM', 'code' => 'ANN', 'description' => 'ANNULE SERVICE EMETTEUR'],
            ['app' => 'BDM', 'code' => 'OUV', 'description' => 'A VALIDER SERVICE DESTINATAIRE'],
            ['app' => 'BDM', 'code' => 'ANN', 'description' => 'ANNULE SERVICE DESTINATAIRE'],
            ['app' => 'BDM', 'code' => 'OUV', 'description' => 'ATTENTE VALIDATION DG'],
            ['app' => 'BDM', 'code' => 'ANN', 'description' => 'ANNULE DG'],
            ['app' => 'BDM', 'code' => 'OUV', 'description' => 'A TRAITER INFO'],
            ['app' => 'BDM', 'code' => 'OUV', 'description' => 'A TRAITER COMPTA'],
            ['app' => 'BDM', 'code' => 'CLO', 'description' => 'CLOTURE COMPTA'],
            ['app' => 'BDM', 'code' => 'ANN', 'description' => 'ANNULE INFORMATIQUE'],

            /** =========== Statuts DIT ===============*/
            ['app' => 'DIT', 'code' => 'CLA', 'description' => 'CLOTUREE ANNULEE'],
            ['app' => 'DIT', 'code' => 'CLV', 'description' => 'CLOTUREE VALIDEE'],
            ['app' => 'DIT', 'code' => 'CLH', 'description' => 'CLOTUREE HORS DELAI'],
            ['app' => 'DIT', 'code' => 'TE', 'description' => 'TERMINEE'],
            ['app' => 'DIT', 'code' => 'AAF', 'description' => 'A AFFECTER'],
            ['app' => 'DIT', 'code' => 'AFF', 'description' => 'AFFECTEE SECTION'],

            /** =========== Statuts TKI ===============*/
            ['app' => 'TKI', 'code' => 'OUV', 'description' => 'OUVERT'],
            ['app' => 'TKI', 'code' => 'REF', 'description' => 'REFUDE'],
            ['app' => 'TKI', 'code' => 'ENC', 'description' => 'ENCOURS'],
            ['app' => 'TKI', 'code' => 'PLA', 'description' => 'PLANIFIE'],
            ['app' => 'TKI', 'code' => 'RES', 'description' => 'RESOLU'],
            ['app' => 'TKI', 'code' => 'ROV', 'description' => 'REOUVERT'],
            ['app' => 'TKI', 'code' => 'CLO', 'description' => 'CLÔTURE'],
            ['app' => 'TKI', 'code' => 'SUS', 'description' => 'SUSPENDU'],

            /** =========== Statuts MUT ===============*/
            ['app' => 'MUT', 'code' => 'OUV', 'description' => 'A VALIDER SERVICE EMETTEUR'],
            ['app' => 'MUT', 'code' => 'OUV', 'description' => 'A VALIDER SERVICE DESTINATAIRE'],
            ['app' => 'MUT', 'code' => 'OUV', 'description' => 'PRE-CONTROLE ATELIER'],
            ['app' => 'MUT', 'code' => 'OUV', 'description' => 'A VALIDER COMPTA'],
            ['app' => 'MUT', 'code' => 'OUV', 'description' => 'A CONTROLER RH'],
            ['app' => 'MUT', 'code' => 'ANN', 'description' => 'ANNULE CHEF DE SERVICE DESTINATAIRE'],
            ['app' => 'MUT', 'code' => 'ANN', 'description' => 'ANNULE CHEF D\'ATELIER'],
            ['app' => 'MUT', 'code' => 'ANN', 'description' => 'ANNULE CHEF DE SERVICE EMMETTEUR'],
            ['app' => 'MUT', 'code' => 'ANN', 'description' => 'ANNULE RH'],
            ['app' => 'MUT', 'code' => 'ANN', 'description' => 'ANNULE COMPTA'],
            ['app' => 'MUT', 'code' => 'CLO', 'description' => 'CLOTURE'],

            /** ================= CC ================= */
            ['app' => 'CC', 'code' => 'OUV', 'description' => 'A VALIDER DA'],
            ['app' => 'CC', 'code' => 'ANN', 'description' => 'ANNULE DA'],
            ['app' => 'CC', 'code' => 'CLO', 'description' => 'CLOTURE INFO'],


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
