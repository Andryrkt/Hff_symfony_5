<?php

namespace App\Service\Migration\Admin\PersonnelUser;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\PersonnelUser\Personnel;
use App\Service\Migration\Utils\EntityRelationMapper;

/**
 * Service de mapping pour la migration des données Personnel
 */
class PersonnelMigrationMapper
{
    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    private EntityRelationMapper $relationMapper;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        EntityRelationMapper $relationMapper
    ) {
        $this->em = $em;
        $this->logger = $logger;
        $this->relationMapper = $relationMapper;
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
        $personnel->setNom($oldData['Nom']);
        $personnel->setPrenoms($oldData['Prenoms']);

        // Champs optionnels
        if (isset($oldData['Matricule'])) {
            $personnel->setMatricule((int) $oldData['Matricule']);
        }

        if (isset($oldData['societe'])) {
            $personnel->setSociete($oldData['societe']);
        }

        if (isset($oldData['Numero_Compte_Bancaire'])) {
            $personnel->setNumeroCompteBancaire($oldData['Numero_Compte_Bancaire']);
        }
    }

    /**
     * Mappe les relations avec d'autres entités
     */
    private function mapRelations(Personnel $personnel, array $oldData): void
    {
        // Relation avec AgenceServiceIrium
        if (!empty($oldData['agence_service_irium_id'])) {
            $agenceServiceIrium = $this->relationMapper->mapAgenceServiceIrium($oldData['agence_service_irium_id']);

            if ($agenceServiceIrium) {
                $personnel->setAgenceServiceIrium($agenceServiceIrium);
            }
        }

        // Note: La relation avec User sera gérée séparément
        // car elle nécessite une migration spécifique des utilisateurs
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
