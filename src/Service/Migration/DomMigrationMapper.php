<?php

namespace App\Service\Migration;

use App\Entity\Hf\Rh\Dom\Dom;
use App\Entity\Hf\Rh\Dom\Site;
use App\Entity\Hf\Rh\Dom\Categorie;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service de mapping des données de l'ancien schéma Dom vers le nouveau
 */
class DomMigrationMapper
{
    private EntityManagerInterface $em;
    private Connection $legacyConnection;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        Connection $legacyConnection,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->legacyConnection = $legacyConnection;
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
            $dateDemande = $this->convertToDate($oldData['Date_Demande']);
            if ($dateDemande) {
                $dom->setDateDemande($dateDemande);
            }
        }

        // Date début (datetime → date)
        if (!empty($oldData['Date_Debut'])) {
            $dateDebut = $this->convertToDate($oldData['Date_Debut']);
            if ($dateDebut) {
                $dom->setDateDebut($dateDebut);
            }
        }

        // Date fin (datetime → date)
        if (!empty($oldData['Date_Fin'])) {
            $dateFin = $this->convertToDate($oldData['Date_Fin']);
            if ($dateFin) {
                $dom->setDateFin($dateFin);
            }
        }

        // Date heure modification statut
        if (!empty($oldData['Date_heure_modif_statut'])) {
            $dateModif = $this->convertToDateTime($oldData['Date_heure_modif_statut']);
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
        // Relation StatutDemande - Recherche par codeApplication + codeStatut
        if (!empty($oldData['ID_Statut_Demande'])) {
            // D'abord essayer par ID (si les IDs correspondent)
            $statut = $this->em->getRepository(StatutDemande::class)
                ->find($oldData['ID_Statut_Demande']);

            if (!$statut) {
                $codeStatut = $this->getStatutDemandeCodeFromLegacy($oldData['ID_Statut_Demande']);
                if ($codeStatut) {
                    $statut = $this->em->getRepository(StatutDemande::class)
                        ->findOneBy([
                            'codeApplication' => 'DOM',
                            'codeStatut' => $codeStatut
                        ]);
                }
            }

            if ($statut) {
                $dom->setIdStatutDemande($statut);
            } else {
                $this->logger->warning('StatutDemande non trouvé', [
                    'ID_Statut_Demande' => $oldData['ID_Statut_Demande'],
                    'Code_Statut' => $oldData['Code_Statut'] ?? null,
                ]);
            }
        }

        // Relation SousTypeDocument - Recherche par codeSousType
        if (!empty($oldData['Sous_Type_Document'])) {
            // D'abord essayer par ID
            $sousType = $this->em->getRepository(SousTypeDocument::class)
                ->find($oldData['Sous_Type_Document']);

            // Si pas trouvé, récupérer le code depuis l'ancienne base
            if (!$sousType) {
                $codeSousType = $this->getSousTypeDocumentCodeFromLegacy($oldData['Sous_Type_Document']);
                if ($codeSousType) {
                    $sousType = $this->em->getRepository(SousTypeDocument::class)
                        ->findOneBy(['codeSousType' => $codeSousType]);
                }
            }

            if ($sousType) {
                $dom->setSousTypeDocument($sousType);
            } else {
                $this->logger->warning('SousTypeDocument non trouvé', [
                    'Sous_Type_Document' => $oldData['Sous_Type_Document'],
                ]);
            }
        }

        // Relation Site - Recherche par nomZone
        if (!empty($oldData['site_id'])) {
            // D'abord essayer par ID
            $site = $this->em->getRepository(Site::class)
                ->find($oldData['site_id']);

            // Si pas trouvé et qu'on a le champ Site (string), chercher par nomZone
            if (!$site && !empty($oldData['Site'])) {
                $site = $this->em->getRepository(Site::class)
                    ->findOneBy(['nomZone' => $oldData['Site']]);
            }

            if ($site) {
                $dom->setSiteId($site);
            } else {
                $this->logger->warning('Site non trouvé', [
                    'site_id' => $oldData['site_id'],
                    'Site' => $oldData['Site'] ?? null,
                ]);
            }
        }

        // Relation Categorie - Recherche par description
        if (!empty($oldData['category_id'])) {
            // D'abord essayer par ID
            $categorie = $this->em->getRepository(Categorie::class)
                ->find($oldData['category_id']);

            // Si pas trouvé et qu'on a le champ Categorie (string), chercher par description
            if (!$categorie && !empty($oldData['Categorie'])) {
                $categorie = $this->em->getRepository(Categorie::class)
                    ->findOneBy(['description' => $oldData['Categorie']]);
            }

            if ($categorie) {
                $dom->setCategoryId($categorie);
            } else {
                $this->logger->warning('Categorie non trouvée', [
                    'category_id' => $oldData['category_id'],
                    'Categorie' => $oldData['Categorie'] ?? null,
                ]);
            }
        }

        // Relations Agence et Service (depuis émetteur)
        if (!empty($oldData['agence_emetteur_id'])) {
            $agence = $this->em->getRepository(Agence::class)
                ->find($oldData['agence_emetteur_id']);

            // Si pas trouvé, récupérer le code depuis l'ancienne base
            if (!$agence) {
                $codeAgence = $this->getAgenceCodeFromLegacy($oldData['agence_emetteur_id']);
                if ($codeAgence) {
                    $agence = $this->em->getRepository(Agence::class)
                        ->findOneBy(['code' => $codeAgence]);
                }
            }

            if ($agence) {
                $dom->setAgenceEmetteurId($agence);
            } else {
                $this->logger->warning('Agence émetteur non trouvée', [
                    'agence_emetteur_id' => $oldData['agence_emetteur_id'],
                ]);
            }
        }

        if (!empty($oldData['service_emetteur_id'])) {
            $service = $this->em->getRepository(Service::class)
                ->find($oldData['service_emetteur_id']);

            // Si pas trouvé, récupérer le code depuis l'ancienne base
            if (!$service) {
                $codeService = $this->getServiceCodeFromLegacy($oldData['service_emetteur_id']);
                if ($codeService) {
                    $service = $this->em->getRepository(Service::class)
                        ->findOneBy(['code' => $codeService]);
                }
            }

            if ($service) {
                $dom->setServiceEmetteurId($service);
            } else {
                $this->logger->warning('Service émetteur non trouvé', [
                    'service_emetteur_id' => $oldData['service_emetteur_id'],
                ]);
            }
        }

        // Relations débiteur
        if (!empty($oldData['agence_debiteur_id'])) {
            $agence = $this->em->getRepository(Agence::class)
                ->find($oldData['agence_debiteur_id']);

            if (!$agence) {
                $codeAgence = $this->getAgenceCodeFromLegacy($oldData['agence_debiteur_id']);
                if ($codeAgence) {
                    $agence = $this->em->getRepository(Agence::class)
                        ->findOneBy(['code' => $codeAgence]);
                }
            }

            if ($agence) {
                $dom->setAgenceDebiteurId($agence);
            }
        }

        if (!empty($oldData['service_debiteur_id'])) {
            $service = $this->em->getRepository(Service::class)
                ->find($oldData['service_debiteur_id']);

            if ($service) {
                $this->logger->info('Service débiteur trouvé par ID', [
                    'service_debiteur_id' => $oldData['service_debiteur_id'],
                    'service_id' => $service->getId(),
                ]);
            }

            if (!$service) {
                $codeService = $this->getServiceCodeFromLegacy($oldData['service_debiteur_id']);
                $this->logger->info('Code service récupéré depuis legacy', [
                    'service_debiteur_id' => $oldData['service_debiteur_id'],
                    'code_service' => $codeService,
                ]);

                if ($codeService) {
                    $service = $this->em->getRepository(Service::class)
                        ->findOneBy(['code' => $codeService]);

                    if ($service) {
                        $this->logger->info('Service débiteur trouvé par code', [
                            'code_service' => $codeService,
                            'service_id' => $service->getId(),
                        ]);
                    } else {
                        $this->logger->warning('Service débiteur non trouvé par code', [
                            'code_service' => $codeService,
                        ]);
                    }
                }
            }

            if ($service) {
                $dom->setServiceDebiteur($service);
            } else {
                $this->logger->warning('Service débiteur non trouvé', [
                    'service_debiteur_id' => $oldData['service_debiteur_id'],
                ]);
            }
        }
    }

    /**
     * Récupère le code Statut depuis l'ancienne base
     */
    private function getStatutDemandeCodeFromLegacy(int $id): ?string
    {
        try {
            $result = $this->legacyConnection->fetchAssociative(
                'SELECT Code_Statut FROM Statut_demande WHERE ID_Statut_Demande = :id',
                ['id' => $id]
            );
            return $result['Code_Statut'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Erreur récupération code statut', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    /**
     * Récupère le code SousTypeDocument depuis l'ancienne base
     */
    private function getSousTypeDocumentCodeFromLegacy(int $id): ?string
    {
        try {
            $result = $this->legacyConnection->fetchAssociative(
                'SELECT Code_Sous_Type FROM Sous_type_document WHERE ID_Sous_Type_Document = :id',
                ['id' => $id]
            );
            return $result['Code_Sous_Type'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Erreur récupération code SousTypeDocument', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Récupère le code Agence depuis l'ancienne base
     */
    private function getAgenceCodeFromLegacy(int $id): ?string
    {
        try {
            $result = $this->legacyConnection->fetchAssociative(
                'SELECT code_agence FROM agences WHERE id = :id',
                ['id' => $id]
            );
            return $result['code_agence'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Erreur récupération code Agence', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Récupère le code Service depuis l'ancienne base
     */
    private function getServiceCodeFromLegacy(int $id): ?string
    {
        try {
            $result = $this->legacyConnection->fetchAssociative(
                'SELECT code_service FROM services WHERE id = :id',
                ['id' => $id]
            );
            return $result['code_service'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Erreur récupération code Service', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Convertit une valeur en DateTime (conserve l'heure)
     */
    private function convertToDateTime($value): ?\DateTimeInterface
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            try {
                return new \DateTime($value);
            } catch (\Exception $e) {
                $this->logger->warning('Impossible de convertir la date/heure', [
                    'value' => $value,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        }

        return null;
    }

    /**
     * Convertit une valeur en Date (perd l'heure)
     */
    private function convertToDate($value): ?\DateTimeInterface
    {
        $datetime = $this->convertToDateTime($value);

        if ($datetime) {
            // Réinitialise l'heure à 00:00:00
            return new \DateTime($datetime->format('Y-m-d'));
        }

        return null;
    }
}
