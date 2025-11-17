<?php

namespace App\DataFixtures\Admin;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Admin\ApplicationGroupe\Permission;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PermissionFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            VignetteFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $permissions = [
            /** === Documentation === */
            ['code' => 'DOC_Annuaire', 'description' => 'liste des annuaires du personnel', 'application' => 'vignette_documentation'],
            ['code' => 'DOC_PLAN_ANALYTIQUE_HFF', 'description' => 'pdf qui liste l\'agence et service avec les nom de responsable', 'application' => 'vignette_documentation'],
            ['code' => 'DOC_INTERNE', 'description' => 'Documentation interne', 'application' => 'vignette_documentation'],
            ['code' => 'DOC_CONTRAT_CREATE', 'description' => 'crée une contrat', 'application' => 'vignette_documentation'],
            ['code' => 'DOC_CONTRAT_VIEW', 'description' => 'consulter les contrat', 'application' => 'vignette_documentation'],

            /** ==== Reporting  ==== */
            ['code' => 'REPORTING_POWER_BI', 'description' => 'Affichage Power BI', 'application' => 'vignette_reporting'],
            ['code' => 'REPORTING_EXCEL', 'description' => 'fichier excel', 'application' => 'vignette_reporting'],

            /** ==== Compta ==== */
            ['code' => 'COMPTA_COURS_DE_CHANGE', 'description' => 'consultation de cours de change', 'application' => 'vignette_compta'],
            ['code' => 'COMPTA_DDP_CREATE', 'description' => 'crée une demande de paiement', 'application' => 'vignette_compta'],
            ['code' => 'COMPTA_DDP_VIEW', 'description' => 'consulter de demande de paiement', 'application' => 'vignette_compta'],
            ['code' => 'COMPTA_BDC_CREAT', 'description' => 'crée une bon de caisse', 'application' => 'vignette_compta'],
            ['code' => 'COMPTA_BDC_VIEW', 'description' => 'consulter la bon de caisse', 'application' => 'vignette_compta'],

            /** === RH ==*/
            //Congé
            ['code' => 'RH_CONGE_CREATE', 'description' => 'Créer une demande de congé', 'application' => 'vignette_rh'],
            ['code' => 'RH_CONGE_VIEW', 'description' => 'Consulter les congés', 'application' => 'vignette_rh'],
            ['code' => 'RH_CONGE_ANNULER', 'description' => 'annuler une congés', 'application' => 'vignette_rh'],
            // mutation
            ['code' => 'RH_MUTATION_CREATE', 'description' => 'Créer une mutation', 'application' => 'vignette_rh'],
            ['code' => 'RH_MUTATION_VIEW', 'description' => 'Consulter les mutations', 'application' => 'vignette_rh'],
            // ordre de mission
            ['code' => 'RH_ORDRE_MISSION_CREATE', 'description' => 'Créer un ordre de mission', 'application' => 'vignette_rh'],
            ['code' => 'RH_ORDRE_MISSION_VIEW', 'description' => 'Consulter les ordres de mission', 'application' => 'vignette_rh'],
            //temporaire
            ['code' => 'RH_TEMPORAIRE_CREATE', 'description' => 'Ajouter un employé temporaire', 'application' => 'vignette_rh'],
            ['code' => 'RH_TEMPORAIRE_VIEW', 'description' => 'Consulter la liste des temporaires', 'application' => 'vignette_rh'],

            /** === Matériel === */
            //BADM
            ['code' => 'MATERIEL_BADM_CREATE', 'description' => 'Créer une demande de mouvement materiel', 'application' => 'vignette_materiel'],
            ['code' => 'MATERIEL_BADM_VIEW', 'description' => 'Consulter les mouvement matériel', 'application' => 'vignette_materiel'],
            //Casier
            ['code' => 'MATERIEL_CASIER_CREATE', 'description' => 'Créer une casier', 'application' => 'vignette_materiel'],
            ['code' => 'MATERIEL_CASIER_VIEW', 'description' => 'Consulter les casiers', 'application' => 'vignette_materiel'],
            ['code' => 'MATERIEL_COMMANDE_MATERIEL', 'description' => 'Commande des matériel', 'application' => 'vignette_materiel'],
            ['code' => 'MATERIEL_SUIVIE', 'description' => 'Suivi administratif des matériels', 'application' => 'vignette_materiel'],


            /** ===  ATELIER  === */
            // demande d'intervention
            ['code' => 'ATELIER_DI_CREATE', 'description' => 'Créer une demande d’intervention', 'application' => 'vignette_atelier'],
            ['code' => 'ATELIER_DI_VIEW', 'description' => 'Consulter les demandes d’intervention', 'application' => 'vignette_atelier'],
            ['code' => 'ATELIER_DI_DOSSIER_DIT', 'description' => 'Consulter les dossier d\'une DIT (les fichiers)', 'application' => 'vignette_atelier'],
            ['code' => 'ATELIER_DI_MATRICE', 'description' => 'fichier excel contient les interface de responsabilité de chaque utilisateur', 'application' => 'vignette_atelier'],
            // glossaire OR
            ['code' => 'ATELIER_GLOSSAIRE_OR', 'description' => 'Une pdf qui decrit l\'OR', 'application' => 'vignette_atelier'],
            // planning - planning détaillé
            ['code' => 'ATELIER_PLANNING', 'description' => 'liste de plannification des OR', 'application' => 'vignette_atelier'],
            ['code' => 'ATELIER_PLANNING_DETAILLE', 'description' => 'liste de plannification des OR détaillé', 'application' => 'vignette_atelier'],
            ['code' => 'ATELIER_PLANNING_ATELIER', 'description' => 'liste de plannification Atelier', 'application' => 'vignette_atelier'],
            // satisfaction client
            ['code' => 'ATELIER_SATISFACTION_CLIENT', 'description' => 'satisfaction client (Atelier excellence survey)', 'application' => 'vignette_atelier'],

            /** ===  Magasin ===*/
            //OR
            ['code' => 'MAGASIN_OR_TRAITER', 'description' => 'Liste des OR à traiter', 'application' => 'vignette_magasin'],
            ['code' => 'MAGASIN_OR_LIVRER', 'description' => 'Liste des OR à livrer', 'application' => 'vignette_magasin'],
            //CIS
            ['code' => 'MAGASIN_CIS_TRAITER', 'description' => 'Liste des CIS à traiter', 'application' => 'vignette_magasin'],
            ['code' => 'MAGASIN_CIS_LIVRER', 'description' => 'Liste des CIS à livrer', 'application' => 'vignette_magasin'],
            //INVENTAIRE
            ['code' => 'MAGASIN_INVENTAIRE', 'description' => 'Liste des inventaire', 'application' => 'vignette_magasin'],
            ['code' => 'MAGASIN_INVENTAIRE_DETAIL', 'description' => 'Liste des inventaire détaillé', 'application' => 'vignette_magasin'],
            // sortie de piece
            ['code' => 'MAGASIN_SORTIE_DE_PIECE', 'description' => 'Demande de sortie de pièce', 'application' => 'vignette_magasin'],
            //Dématérialisation
            ['code' => 'MAGASIN_DEMAT_DEVIS', 'description' => 'dematérialisation de devis magasin', 'application' => 'vignette_magasin'],
            ['code' => 'MAGASIN_DEMAT_CMD_CLIENT', 'description' => 'dématérialisation des commandes client', 'application' => 'vignette_magasin'],
            ['code' => 'MAGASIN_DEMAT_PLANNING', 'description' => 'démaérialisaiton de planning magasin', 'application' => 'vignette_magasin'],
            ['code' => 'MAGASIN_CMD_FOURNISSEUR', 'description' => 'Soumission des commandes fournisseur', 'application' => 'vignette_magasin'],
            ['code' => 'MAGASIN_CMD_NON_PLACEE', 'description' => 'Liste des commandes non placées', 'application' => 'vignette_magasin'],


            /** ==== Appro ==== */
            ['code' => 'APPRO_CREATE', 'description' => 'crée une demande d\'approvisionnement', 'application' => 'vignette_appro'],
            ['code' => 'APPRO_VIEW', 'description' => 'Consulté les demandes d\'approvisionnement', 'application' => 'vignette_appro'],
            ['code' => 'APPRO_CMD_FRN', 'description' => 'Consulté les commande fournisseur de DA', 'application' => 'vignette_appro'],

            /** ==== IT ==== */
            ['code' => 'IT_CREATE', 'description' => 'crée une ticket', 'application' => 'vignette_it'],
            ['code' => 'IT_VIEW', 'description' => 'Consulté les ticket', 'application' => 'vignette_it'],
            ['code' => 'IT_PLANNING', 'description' => 'Visualisé les ticket palnnifié', 'application' => 'vignette_it'],

            /** ==== POL ==== */
            ['code' => 'POL_DLUB_CREAT', 'description' => 'Creation de DLUB', 'application' => 'vignette_pol'],
            ['code' => 'POL_VIEWS', 'description' => 'Consulter les DLUB', 'application' => 'vignette_pol'],
            ['code' => 'POL_CMD_FRN', 'description' => 'Liste des commandes fournisseur', 'application' => 'vignette_pol'],
            ['code' => 'POL_PNEUMATIQUE', 'description' => 'pneumatique', 'application' => 'vignette_pol'],

            /** ==== ENERGIE ==== */
            ['code' => 'ENERGIE_RAPPORT_PROD', 'description' => 'Rapport de production central', 'application' => 'vignette_energie'],

            /** ==== HSE ==== */
            ['code' => 'HSE_RAPPORT_INCIDENT', 'description' => 'Rapport d\'incident', 'application' => 'vignette_hse'],
            ['code' => 'HSE_DOC', 'description' => 'Documentaion', 'application' => 'vignette_hse'],

        ];

        foreach ($permissions as $permissionData) {
            $permission = new Permission();
            $permission->setCode($permissionData['code'])
                ->setDescription($permissionData['description'])
                ->setVignette($this->getReference($permissionData['application']))
            ;

            $manager->persist($permission);
            $this->addReference('permission_'.$permissionData['code'], $permission);
        }

        $manager->flush();
    }
}

