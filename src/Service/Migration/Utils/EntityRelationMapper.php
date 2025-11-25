<?php

namespace App\Service\Migration\Utils;

use Psr\Log\LoggerInterface;
use App\Entity\Hf\Rh\Dom\Site;
use App\Entity\Hf\Rh\Dom\Categorie;
use App\Entity\Admin\PersonnelUser\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use App\Entity\Admin\AgenceService\Service;

/**
 * Service dédié au mapping des relations entre entités
 */
class EntityRelationMapper
{
    private EntityManagerInterface $em;
    private LegacyDataFetcher $legacyDataFetcher;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        LegacyDataFetcher $legacyDataFetcher,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->legacyDataFetcher = $legacyDataFetcher;
        $this->logger = $logger;
    }

    /**
     * Mappe la relation StatutDemande
     * Stratégie : ID → Code (via legacy DB)
     */
    public function mapStatutDemande(array $oldData): ?StatutDemande
    {
        if (empty($oldData['ID_Statut_Demande'])) {
            return null;
        }

        // D'abord essayer par ID
        $statut = $this->em->getRepository(StatutDemande::class)
            ->find($oldData['ID_Statut_Demande']);

        if (!$statut) {
            $codeStatut = $this->legacyDataFetcher->getStatutDemandeCode($oldData['ID_Statut_Demande']);
            if ($codeStatut) {
                $statut = $this->em->getRepository(StatutDemande::class)
                    ->findOneBy([
                        'codeApplication' => 'DOM',
                        'codeStatut' => $codeStatut
                    ]);
            }
        }

        if (!$statut) {
            $this->logger->warning('StatutDemande non trouvé', [
                'ID_Statut_Demande' => $oldData['ID_Statut_Demande'],
                'Code_Statut' => $oldData['Code_Statut'] ?? null,
            ]);
        }

        return $statut;
    }

    /**
     * Mappe la relation SousTypeDocument
     * Stratégie : ID → Code (via legacy DB)
     */
    public function mapSousTypeDocument(array $oldData): ?SousTypeDocument
    {
        if (empty($oldData['Sous_Type_Document'])) {
            return null;
        }

        // D'abord essayer par ID
        $sousType = $this->em->getRepository(SousTypeDocument::class)
            ->find($oldData['Sous_Type_Document']);

        // Si pas trouvé, récupérer le code depuis l'ancienne base
        if (!$sousType) {
            $codeSousType = $this->legacyDataFetcher->getSousTypeDocumentCode($oldData['Sous_Type_Document']);
            if ($codeSousType) {
                $sousType = $this->em->getRepository(SousTypeDocument::class)
                    ->findOneBy(['codeSousType' => $codeSousType]);
            }
        }

        if (!$sousType) {
            $this->logger->warning('SousTypeDocument non trouvé', [
                'Sous_Type_Document' => $oldData['Sous_Type_Document'],
            ]);
        }

        return $sousType;
    }

    /**
     * Mappe la relation Site
     * Stratégie : ID → Nom (depuis oldData)
     */
    public function mapSite(array $oldData): ?Site
    {
        if (empty($oldData['site_id'])) {
            return null;
        }

        // D'abord essayer par ID
        $site = $this->em->getRepository(Site::class)
            ->find($oldData['site_id']);

        // Si pas trouvé et qu'on a le champ Site (string), chercher par nomZone
        if (!$site && !empty($oldData['Site'])) {
            $site = $this->em->getRepository(Site::class)
                ->findOneBy(['nomZone' => $oldData['Site']]);
        }

        if (!$site) {
            $this->logger->warning('Site non trouvé', [
                'site_id' => $oldData['site_id'],
                'Site' => $oldData['Site'] ?? null,
            ]);
        }

        return $site;
    }

    /**
     * Mappe la relation Categorie
     * Stratégie : ID → Description (depuis oldData)
     */
    public function mapCategorie(array $oldData): ?Categorie
    {
        if (empty($oldData['category_id'])) {
            return null;
        }

        // D'abord essayer par ID
        $categorie = $this->em->getRepository(Categorie::class)
            ->find($oldData['category_id']);

        // Si pas trouvé et qu'on a le champ Categorie (string), chercher par description
        if (!$categorie && !empty($oldData['Categorie'])) {
            $categorie = $this->em->getRepository(Categorie::class)
                ->findOneBy(['description' => $oldData['Categorie']]);
        }

        if (!$categorie) {
            $this->logger->warning('Categorie non trouvée', [
                'category_id' => $oldData['category_id'],
                'Categorie' => $oldData['Categorie'] ?? null,
            ]);
        }

        return $categorie;
    }

    /**
     * Mappe une Agence
     * Stratégie : ID → Code (via legacy DB)
     */
    public function mapAgence(int $id): ?Agence
    {
        $agence = $this->em->getRepository(Agence::class)->find($id);

        // Si pas trouvé, récupérer le code depuis l'ancienne base
        if (!$agence) {
            $codeAgence = $this->legacyDataFetcher->getAgenceCode($id);
            if ($codeAgence) {
                $agence = $this->em->getRepository(Agence::class)
                    ->findOneBy(['code' => $codeAgence]);
            }
        }

        if (!$agence) {
            $this->logger->warning('Agence non trouvée', ['id' => $id]);
        }

        return $agence;
    }

    /**
     * Mappe un Service
     * Stratégie : ID → Code (via legacy DB)
     */
    public function mapService(int $id, bool $logDetails = false): ?Service
    {
        $service = $this->em->getRepository(Service::class)->find($id);

        if ($service && $logDetails) {
            $this->logger->info('Service trouvé par ID', [
                'id' => $id,
                'service_id' => $service->getId(),
            ]);
        }

        if (!$service) {
            $codeService = $this->legacyDataFetcher->getServiceCode($id);

            if ($logDetails) {
                $this->logger->info('Code service récupéré depuis legacy', [
                    'id' => $id,
                    'code_service' => $codeService,
                ]);
            }

            if ($codeService) {
                $service = $this->em->getRepository(Service::class)
                    ->findOneBy(['code' => $codeService]);

                if ($service && $logDetails) {
                    $this->logger->info('Service trouvé par code', [
                        'code_service' => $codeService,
                        'service_id' => $service->getId(),
                    ]);
                }
            }
        }

        if (!$service) {
            $this->logger->warning('Service non trouvé', ['id' => $id]);
        }

        return $service;
    }

    /**
     * Mappe un User par username
     */
    public function mapUser(string $username): ?User
    {
        if (empty($username)) {
            return null;
        }

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (!$user) {
            $this->logger->warning('User non trouvé', ['username' => $username]);
        }

        return $user;
    }
}
