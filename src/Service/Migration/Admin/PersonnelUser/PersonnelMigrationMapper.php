<?php

namespace App\Service\Migration\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use App\Service\Migration\Utils\DateTimeConverter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service de mapping pour la migration des données Personnel
 */
class PersonnelMigrationMapper
{
    private EntityManagerInterface $em;
    private DateTimeConverter $dateTimeConverter;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        DateTimeConverter $dateTimeConverter,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->dateTimeConverter = $dateTimeConverter;
        $this->logger = $logger;
    }

    /**
     * Mappe les données de l'ancienne structure vers la nouvelle entité Personnel
     * 
     * @param array $oldData Données depuis la table legacy (ex: personnels)
     * @return Personnel|null
     */
    public function mapOldToNew(array $oldData): ?Personnel
    {
        try {
            // Vérification des champs obligatoires
            if (empty($oldData['nom']) || empty($oldData['prenoms'])) {
                $this->logger->warning('Personnel ignoré : nom ou prénoms manquants', [
                    'old_id' => $oldData['id'] ?? 'unknown',
                    'data' => $oldData,
                ]);
                return null;
            }

            $personnel = new Personnel();

            // Mapping des champs simples
            $this->mapSimpleFields($personnel, $oldData);

            // Mapping des relations
            $this->mapRelations($personnel, $oldData);

            return $personnel;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du mapping Personnel', [
                'old_id' => $oldData['id'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Mappe les champs simples
     */
    private function mapSimpleFields(Personnel $personnel, array $oldData): void
    {
        // Champs obligatoires
        $personnel->setNom($oldData['nom']);
        $personnel->setPrenoms($oldData['prenoms']);

        // Champs optionnels
        if (isset($oldData['matricule'])) {
            $personnel->setMatricule((int) $oldData['matricule']);
        }

        if (isset($oldData['societe'])) {
            $personnel->setSociete($oldData['societe']);
        }

        if (isset($oldData['numero_compte_bancaire'])) {
            $personnel->setNumeroCompteBancaire($oldData['numero_compte_bancaire']);
        }
    }

    /**
     * Mappe les relations avec d'autres entités
     */
    private function mapRelations(Personnel $personnel, array $oldData): void
    {
        // Relation avec AgenceServiceIrium
        if (!empty($oldData['agence_service_irium_id'])) {
            $agenceServiceIrium = $this->mapAgenceServiceIrium($oldData['agence_service_irium_id']);
            if ($agenceServiceIrium) {
                $personnel->setAgenceServiceIrium($agenceServiceIrium);
            }
        }

        // Note: La relation avec User sera gérée séparément
        // car elle nécessite une migration spécifique des utilisateurs
    }

    /**
     * Mappe la relation AgenceServiceIrium
     */
    private function mapAgenceServiceIrium(int $id): ?AgenceServiceIrium
    {
        $agenceServiceIrium = $this->em->getRepository(AgenceServiceIrium::class)->find($id);

        if (!$agenceServiceIrium) {
            $this->logger->warning('AgenceServiceIrium non trouvé', [
                'id' => $id,
            ]);
        }

        return $agenceServiceIrium;
    }

    /**
     * Trouve un Personnel existant par matricule pour éviter les doublons
     * Utile pour la synchronisation incrémentale
     */
    public function findExistingByMatricule(?int $matricule): ?Personnel
    {
        if ($matricule === null) {
            return null;
        }

        return $this->em->getRepository(Personnel::class)
            ->findOneBy(['matricule' => $matricule]);
    }

    /**
     * Met à jour un Personnel existant avec les nouvelles données
     * Utilisé pour la synchronisation incrémentale
     */
    public function updateExisting(Personnel $personnel, array $oldData): Personnel
    {
        $this->mapSimpleFields($personnel, $oldData);
        $this->mapRelations($personnel, $oldData);

        return $personnel;
    }
}
