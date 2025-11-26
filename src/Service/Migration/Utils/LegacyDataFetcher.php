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
     * Récupère le code Statut depuis l'ancienne base - Statut_demande
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
     * Récupère le code SousTypeDocument depuis l'ancienne base - Sous_type_document
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
     * Récupère le code Agence depuis l'ancienne base - agences
     * ex: 80
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
     * Récupère le code Service depuis l'ancienne base - services
     * ex: INF
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

    /**
     * Récupère le code agence - code service depuis l'ancien base - Agence_Service_Irium
     * ex: 80-INF
     */
    public function getCodeAgenceService(int $id): ?string
    {
        try {
            $result = $this->legacyConnection->fetchAssociative(
                "SELECT CONCAT_WS('-', agence_ips, service_ips) AS ag_serv 
                FROM Agence_Service_Irium 
                WHERE id = :id",
                ['id' => $id]
            );

            return $result['ag_serv'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération du code agence-service', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
