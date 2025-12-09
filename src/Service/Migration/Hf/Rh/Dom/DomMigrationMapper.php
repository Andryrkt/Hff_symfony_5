<?php

namespace App\Service\Migration\Hf\Rh\Dom;

use Psr\Log\LoggerInterface;
use App\Entity\Hf\Rh\Dom\Dom;
use App\Service\Migration\Utils\DateTimeConverter;
use App\Service\Migration\Utils\EntityRelationMapper;

/**
 * Service de mapping des données de l'ancien schéma Dom vers le nouveau
 */
class DomMigrationMapper
{

    private EntityRelationMapper $relationMapper;
    private DateTimeConverter $dateTimeConverter;
    private LoggerInterface $logger;

    public function __construct(
        EntityRelationMapper $relationMapper,
        DateTimeConverter $dateTimeConverter,
        LoggerInterface $logger
    ) {
        $this->relationMapper = $relationMapper;
        $this->dateTimeConverter = $dateTimeConverter;
        $this->logger = $logger;
    }

    /**
     * Mappe un enregistrement de l'ancien schéma vers une nouvelle entité Dom
     *
     * @param array $oldData Données de l'ancien schéma
     * @return Dom|null Nouvelle entité Dom ou null en cas d'erreur
     */
    public function mapOldToNew(array $oldData): ?Dom
    {
        try {
            $dom = new Dom();

            // Mapping des champs simples
            $this->mapSimpleFields($dom, $oldData);

            // Mapping des dates
            $this->mapDateFields($dom, $oldData);

            // Mapping des relations
            $this->mapRelations($dom, $oldData);

            return $dom;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du mapping des données DOM', [
                'error' => $e->getMessage(),
                'old_id' => $oldData['ID_Demande_Ordre_Mission'] ?? 'unknown',
            ]);
            return null;
        }
    }

    /**
     * Mappe les champs simples (string, int, etc.)
     */
    private function mapSimpleFields(Dom $dom, array $oldData): void
    {
        // Champs obligatoires
        $dom->setNumeroOrdreMission($oldData['Numero_Ordre_Mission'] ?? '');
        $dom->setNomSessionUtilisateur($oldData['Nom_Session_Utilisateur'] ?? '');
        $dom->setMotifDeplacement($oldData['Motif_Deplacement'] ?? '');
        $dom->setClient($oldData['Client'] ?? '');
        $dom->setLieuIntervention($oldData['Lieu_Intervention'] ?? '');
        $dom->setVehiculeSociete($oldData['Vehicule_Societe'] ?? 'NON');

        // Heures
        $dom->setHeureDebut($oldData['Heure_Debut'] ?? '00:00');
        $dom->setHeureFin($oldData['Heure_Fin'] ?? '00:00');

        // Nombre de jours
        $dom->setNombreJour((int)($oldData['Nombre_Jour'] ?? 0));

        // Totaux
        $dom->setTotalIndemniteForfaitaire($oldData['Total_Indemnite_Forfaitaire'] ?? '0');

        // Champs optionnels
        $dom->setMatricule($oldData['Matricule'] ?? null);
        $dom->setIndemniteForfaitaire($oldData['Indemnite_Forfaitaire'] ?? null);
        $dom->setMotifAutreDepense1($oldData['Motif_Autres_depense_1'] ?? null);
        $dom->setAutresDepense1($oldData['Autres_depense_1'] ?? null);
        $dom->setMotifAutresDepense2($oldData['Motif_Autres_depense_2'] ?? null);
        $dom->setAutresDepense2($oldData['Autres_depense_2'] ?? null);
        $dom->setMotifAutresDepense3($oldData['Motif_Autres_depense_3'] ?? null);
        $dom->setAutresDepense3($oldData['Autres_depense_3'] ?? null);
        $dom->setTotalAutresDepenses($oldData['Total_Autres_Depenses'] ?? null);
        $dom->setTotalGeneralPayer($oldData['Total_General_Payer'] ?? null);
        $dom->setModePayement($oldData['Mode_Paiement'] ?? null);

        // Pièces jointes
        $dom->setPieceJoint01($oldData['Piece_Jointe_1'] ?? null);
        $dom->setPieceJoint02($oldData['Piece_Jointe_2'] ?? null);
        $dom->setPieceJoint3($oldData['Piece_Jointe_3'] ?? null);

        // Informations personnelles
        $dom->setNumeroTel($oldData['Numero_Tel'] ?? null);
        $dom->setNom($oldData['Nom'] ?? null);
        $dom->setPrenom($oldData['Prenom'] ?? null);
        $dom->setDevis($oldData['Devis'] ?? null);
        $dom->setLibelleCodeAgenceService($oldData['LibelleCodeAgence_Service'] ?? null);
        $dom->setFiche($oldData['Fiche'] ?? null);
        $dom->setNumVehicule($oldData['NumVehicule'] ?? null);
        $dom->setDroitIndemnite($oldData['Doit_indemnite'] ?? null);

        // Champs string pour catégorie et site (à migrer vers relations)
        $dom->setCategorie($oldData['Categorie'] ?? null);
        $dom->setSite($oldData['Site'] ?? null);
        $dom->setIdemnityDepl($oldData['idemnity_depl'] ?? null);

        // Statut
        $dom->setCodeStatut($oldData['Code_Statut'] ?? null);
        $dom->setPieceJustificatif($oldData['piece_justificatif'] ?? false);

        // Champs obsolètes de l'ancien schéma (émetteur/débiteur)
        $dom->setEmetteur($oldData['Emetteur'] ?? null);
        $dom->setDebiteur($oldData['Debiteur'] ?? null);
    }

    /**
     * Mappe les champs de type date/datetime
     */
    private function mapDateFields(Dom $dom, array $oldData): void
    {
        // Date de demande (datetime → date)
        if (!empty($oldData['Date_Demande'])) {
            $dateDemande = $this->dateTimeConverter->convertToDate($oldData['Date_Demande']);
            if ($dateDemande) {
                $dom->setDateDemande($dateDemande);
            }
        }

        // Date début (datetime → date)
        if (!empty($oldData['Date_Debut'])) {
            $dateDebut = $this->dateTimeConverter->convertToDate($oldData['Date_Debut']);
            if ($dateDebut) {
                $dom->setDateDebut($dateDebut);
            }
        }

        // Date fin (datetime → date)
        if (!empty($oldData['Date_Fin'])) {
            $dateFin = $this->dateTimeConverter->convertToDate($oldData['Date_Fin']);
            if ($dateFin) {
                $dom->setDateFin($dateFin);
            }
        }

        // Date heure modification statut
        if (!empty($oldData['Date_heure_modif_statut'])) {
            $dateModif = $this->dateTimeConverter->convertToDateTime($oldData['Date_heure_modif_statut']);
            if ($dateModif) {
                $dom->setDateHeureModifStatut($dateModif);
            }
        }
    }

    /**
     * Mappe les relations ManyToOne
     */
    private function mapRelations(Dom $dom, array $oldData): void
    {
        // Relation StatutDemande
        $statut = $this->relationMapper->mapStatutDemande($oldData);
        if ($statut) {
            $dom->setIdStatutDemande($statut);
        }

        // Relation SousTypeDocument
        $sousType = $this->relationMapper->mapSousTypeDocument($oldData);
        if ($sousType) {
            $dom->setSousTypeDocument($sousType);
        }

        // Relation Site
        $site = $this->relationMapper->mapSite($oldData);
        if ($site) {
            $dom->setSiteId($site);
        }

        // Relation Categorie
        $categorie = $this->relationMapper->mapCategorie($oldData);
        if ($categorie) {
            $dom->setCategoryId($categorie);
        }

        // Relations Agence et Service émetteur
        if (!empty($oldData['agence_emetteur_id'])) {
            $agence = $this->relationMapper->mapAgence($oldData['agence_emetteur_id']);
            if ($agence) {
                $dom->setAgenceEmetteurId($agence);
            }
        }

        if (!empty($oldData['service_emetteur_id'])) {
            $service = $this->relationMapper->mapService($oldData['service_emetteur_id']);
            if ($service) {
                $dom->setServiceEmetteurId($service);
            }
        }

        // Relations Agence et Service débiteur
        if (!empty($oldData['agence_debiteur_id'])) {
            $agence = $this->relationMapper->mapAgence($oldData['agence_debiteur_id']);
            if ($agence) {
                $dom->setAgenceDebiteurId($agence);
            }
        }

        if (!empty($oldData['service_debiteur_id'])) {
            $service = $this->relationMapper->mapService($oldData['service_debiteur_id'], true);
            if ($service) {
                $dom->setServiceDebiteur($service);
            }
        }

        // Relation CreatedBy
        if (!empty($oldData['Nom_Session_Utilisateur'])) {
            $user = $this->relationMapper->mapUser($oldData['Nom_Session_Utilisateur']);
            if ($user) {
                $dom->setCreatedBy($user);
            }
        }
    }
}
