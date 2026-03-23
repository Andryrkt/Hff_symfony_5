<?php

namespace App\Service\Migration\Hf\Atelier\Dit\Soumission\Ors;

use App\Entity\Hf\Atelier\Dit\Soumission\Ors\Ors;
use Psr\Log\LoggerInterface;

class OrsMigrationMapper
{
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Mappe un enregistrement de l'ancien schéma vers une nouvelle entité Ors
     *
     * @param array $oldData Données de l'ancien schéma
     * @return Ors|null Nouvelle entité Ors ou null en cas d'erreur
     */
    public function mapOldToNew(array $oldData): ?Ors
    {
        try {
            $ors = new Ors();

            // Mapping des champs simples
            $this->mapSimpleFields($ors, $oldData);

            return $ors;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du mapping des données ORS', [
                'error' => $e->getMessage(),
                'old_id' => $oldData['ID_ORS'] ?? 'unknown', // ID supposé
            ]);
            return null;
        }
    }

    private function mapSimpleFields(Ors $ors, array $oldData): void
    {
        $ors
            ->setNumeroDit($oldData['numeroDIT'] ?? null)
            ->setNumeroOr((int) ($oldData['numeroOR'] ?? 0))
            ->setNumeroVersion((int) ($oldData['numeroVersion'] ?? 1))
            ->setStatut($oldData['statut'] ?? 'En attente')
            ->setPieceFaibleActiviteAchat(isset($oldData['piece_faible_activite_achat']) ? (bool) $oldData['piece_faible_activite_achat'] : null)
            ->setNumeroItv((int) ($oldData['numeroItv'] ?? 0))
            ->setNombreLigneItv((int) ($oldData['nombreLigneItv'] ?? 0))
            ->setMontantItv((float) ($oldData['montantItv'] ?? 0.0))
            ->setMontantPiece((float) ($oldData['montantPiece'] ?? 0.0))
            ->setMontantMo((float) ($oldData['montantMo'] ?? 0.0))
            ->setMontantAchatLocaux((float) ($oldData['montantAchatLocaux'] ?? 0.0))
            ->setMontantFraisDivers((float) ($oldData['montantFraisDivers'] ?? 0.0))
            ->setMontantLubrifiants((float) ($oldData['montantLubrifiants'] ?? 0.0))
            ->setLibellelItv($oldData['libellelItv'] ?? null)
            ->setObservation($oldData['observation'] ?? null)
            ->setMigration(isset($oldData['migration']) ? (int) $oldData['migration'] : null);
    }
}
