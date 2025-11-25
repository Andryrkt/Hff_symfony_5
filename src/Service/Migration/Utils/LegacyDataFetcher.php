<?php

namespace App\Service\Migration\Utils;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

/**
 * Service dédié à la récupération de données depuis l'ancienne base de données
 */
class LegacyDataFetcher
{
    private Connection $legacyConnection;
    private LoggerInterface $logger;

    public function __construct(
        Connection $legacyConnection,
        LoggerInterface $logger
    ) {
        $this->legacyConnection = $legacyConnection;
        $this->logger = $logger;
    }

    /**
     * Récupère le code Statut depuis l'ancienne base
     */
    public function getStatutDemandeCode(int $id): ?string
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
    public function getSousTypeDocumentCode(int $id): ?string
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
    public function getAgenceCode(int $id): ?string
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
    public function getServiceCode(int $id): ?string
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
}
