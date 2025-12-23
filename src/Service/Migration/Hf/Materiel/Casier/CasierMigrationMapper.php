<?php

namespace App\Service\Migration\Hf\Materiel\Casier;

use Psr\Log\LoggerInterface;
use App\Entity\Hf\Materiel\Casier\Casier;
use App\Service\Migration\Utils\EntityRelationMapper;

class CasierMigrationMapper
{
    private EntityRelationMapper $relationMapper;
    private LoggerInterface $logger;

    public function __construct(
        EntityRelationMapper $relationMapper,
        LoggerInterface $logger
    ) {
        $this->relationMapper = $relationMapper;
        $this->logger = $logger;
    }

    public function mapOldToNew(array $oldData): ?Casier
    {
        try {
            $casier = new Casier();

            // Mapping des champs simples
            $this->mapSimpleFields($casier, $oldData);

            // Mapping des relations
            $this->mapRelations($casier, $oldData);

            return $casier;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du mapping des données CASIER', [
                'error' => $e->getMessage(),
                'old_id' => $oldData['Id'] ?? 'unknown',
            ]);
            return null;
        }
    }

    /**
     * Mappe les champs simples (string, int, etc.)
     */
    private function mapSimpleFields(Casier $casier, array $oldData): void
    {
        // Champs obligatoires
        $casier->setNumero($oldData['Numero_CAS'] ?? '');
        $casier->setNom($oldData['Casier'] ?? '');
        $casier->setIsValide(true);
    }

    /**
     * Mappe les relations ManyToOne
     */
    private function mapRelations(Casier $casier, array $oldData): void
    {
        // Relation StatutDemande
        $statut = $this->relationMapper->mapStatutDemande($oldData);
        if ($statut) {
            $casier->setStatutDemande($statut);
        }

        // Relations Agence rattacher
        if (!empty($oldData['Agence_Rattacher'])) {
            $agence = $this->relationMapper->mapAgence($oldData['Agence_Rattacher']);
            if ($agence) {
                $casier->setAgenceRattacher($agence);
            }
        }

        // Relation created by (crée par)
        if (!empty($oldData['Nom_Session_Utilisateur'])) {
            $user = $this->relationMapper->mapUserWithId($oldData['Nom_Session_Utilisateur']);
            if ($user) {
                $casier->setCreatedBy($user);
            }
        }
    }
}
