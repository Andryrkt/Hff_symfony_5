<?php

namespace App\Service\Migration\Hf\Materiel\Badm;

use App\Entity\Hf\Materiel\Badm\Badm;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;
use App\Entity\Hf\Materiel\Casier\Casier;
use App\Service\Migration\Utils\DateTimeConverter;
use App\Service\Migration\Utils\EntityRelationMapper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;

/**
 * Service de mapping des données de l'ancien schéma BADM vers le nouveau
 */
class BadmMigrationMapper
{
    private EntityRelationMapper $relationMapper;
    private DateTimeConverter $dateTimeConverter;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    private Connection $legacyConnection;

    public function __construct(
        EntityRelationMapper $relationMapper,
        DateTimeConverter $dateTimeConverter,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        Connection $legacyConnection
    ) {
        $this->relationMapper = $relationMapper;
        $this->dateTimeConverter = $dateTimeConverter;
        $this->em = $em;
        $this->logger = $logger;
        $this->legacyConnection = $legacyConnection;
    }

    /**
     * Mappe un enregistrement de l'ancien schéma vers une nouvelle entité Badm
     *
     * @param array $oldData Données de l'ancien schéma
     * @return Badm|null Nouvelle entité Badm ou null en cas d'erreur
     */
    public function mapOldToNew(array $oldData): ?Badm
    {
        try {
            $badm = new Badm();

            // Mapping des champs simples
            $this->mapSimpleFields($badm, $oldData);

            // Mapping des dates
            $this->mapDateFields($badm, $oldData);

            // Mapping des relations
            $this->mapRelations($badm, $oldData);

            return $badm;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du mapping des données BADM', [
                'error' => $e->getMessage(),
                'old_id' => $oldData['ID_BADM'] ?? 'unknown', // ID supposé
            ]);
            return null;
        }
    }

    private function mapSimpleFields(Badm $badm, array $oldData): void
    {
        // Champs correspondants supposés
        $badm->setNumeroBadm($oldData['Numero_Demande_BADM'] ?? '');
        $badm->setIdMateriel((int)($oldData['ID_Materiel'] ?? 0));
        $badm->setMotifMateriel($oldData['motif_materiel'] ?? null);
        $badm->setEtatAchat($oldData['etat_achat'] ?? 'NEUF');


        $badm->setCoutAcquisition((float)($oldData['Cout_Acquisition'] ?? 0));
        $badm->setAmortissement((float)($oldData['Amortissement'] ?? 0));
        $badm->setValeurNetComptable((float)($oldData['Valeur_Net_Comptable'] ?? 0));

        $badm->setNomClient($oldData['Nom_Client'] ?? null);
        $badm->setModalitePaiement($oldData['Modalite_Paiement'] ?? null);
        $badm->setPrixVenteHt((float)($oldData['Prix_Vente_HT'] ?? 0));
        $badm->setMotifMiseRebut($oldData['Motif_Mise_Rebut'] ?? null);

        $badm->setHeureMachine((int)($oldData['Heure_machine'] ?? 0));
        $badm->setKmMachine((int)($oldData['KM_machine'] ?? 0));
        $badm->setNumParc($oldData['Num_Parc'] ?? null);

        $badm->setNomImage($oldData['Nom_Image'] ?? null);
        $badm->setNomFichier($oldData['Nom_Fichier'] ?? null);
    }

    private function mapDateFields(Badm $badm, array $oldData): void
    {
        // Date mise en location
        if (!empty($oldData['Date_Mise_Location'])) {
            $date = $this->dateTimeConverter->convertToDate($oldData['Date_Mise_Location']);
            if ($date) {
                $badm->setDateMiseLocation($date);
            }
        }
    }

    private function mapRelations(Badm $badm, array $oldData): void
    {
        // Relation TypeMouvement
        if (!empty($oldData['Code_Mouvement'])) {
            $typeMouvement = $this->findTypeMouvement($oldData['Code_Mouvement']);
            if ($typeMouvement) {
                $badm->setTypeMouvement($typeMouvement);
            }
        }

        // CreatedBy (User)
        if (!empty($oldData['Nom_Session_Utilisateur'])) {
            $user = $this->relationMapper->mapUser($oldData['Nom_Session_Utilisateur']);
            if ($user) {
                $badm->setCreatedBy($user);
            }
        }

        // Relation StatutDemande
        $statut = $this->relationMapper->mapStatutDemande($oldData, 'BDM');
        if ($statut) {
            $badm->setStatutDemande($statut);
        }


        // Relations Casier (Emetteur / Destinataire)
        if (!empty($oldData['Casier_Emetteur'])) {
            $casier = $this->findCasierWithNom($oldData['Casier_Emetteur']);
            if ($casier) {
                $badm->setCasierEmetteur($casier);
            }
        }

        if (!empty($oldData['Casier_Destinataire'])) {
            $casier = $this->findCasierWithId($oldData['Casier_Destinataire']);
            if ($casier) {
                $badm->setCasierDestinataire($casier);
            }
        }

        // Relations Agence et Service émetteur
        if (!empty($oldData['agence_emetteur_id'])) {
            $agence = $this->relationMapper->mapAgence($oldData['agence_emetteur_id']);
            if ($agence) {
                $badm->setAgenceEmetteurId($agence);
            }
        }

        if (!empty($oldData['service_emetteur_id'])) {
            $service = $this->relationMapper->mapService($oldData['service_emetteur_id']);
            if ($service) {
                $badm->setServiceEmetteurId($service);
            }
        }

        // Relations Agence et Service débiteur
        if (!empty($oldData['agence_debiteur_id'])) {
            $agence = $this->relationMapper->mapAgence($oldData['agence_debiteur_id']);
            if ($agence) {
                $badm->setAgenceDebiteurId($agence);
            }
        }

        if (!empty($oldData['service_debiteur_id'])) {
            $service = $this->relationMapper->mapService($oldData['service_debiteur_id'], true);
            if ($service) {
                $badm->setServiceDebiteur($service);
            }
        }
    }

    /**
     * recupère le casier avec l'ID
     */
    private function findCasierWithId($id): ?Casier
    {
        if (empty($id)) {
            return null;
        }
        $nomCasier = $this->findNomCasier($id);
        if (empty($nomCasier)) {
            return null;
        }
        return $this->em->getRepository(Casier::class)->findOneBy(['nom' => $nomCasier]);
    }

    /**
     * Recupère le nom du casier dans l'ancien base de donnée avec l'ID
     */
    private function findNomCasier(int $id): ?string
    {
        try {
            $result = $this->legacyConnection->fetchAssociative(
                'SELECT Casier FROM Casier_Materiels WHERE id = :id',
                ['id' => $id]
            );
            return $result['Casier'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Erreur récupération du nom du casier', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Recupère le casier avec le nom
     */
    private function findCasierWithNom($nom): ?Casier
    {
        if (empty($nom)) {
            return null;
        }
        return $this->em->getRepository(Casier::class)->findOneBy(['nom' => $nom]);
    }

    /**
     * Recupère le type de mouvement avec l'id
     */
    private function findTypeMouvement($id): ?TypeMouvement
    {
        if (empty($id)) {
            return null;
        }
        $description = $this->finddescriptionTypeMouvement($id);
        return $this->em->getRepository(TypeMouvement::class)->findOneBy(['description' => $description]);
    }

    private function finddescriptionTypeMouvement(int $id): ?string
    {
        try {
            $result = $this->legacyConnection->fetchAssociative(
                'SELECT description FROM Type_Mouvement WHERE ID_Type_Mouvement = :id',
                ['id' => $id]
            );
            return $result['description'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Erreur récupération du description du type de mouvement', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
