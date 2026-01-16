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
use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Admin\AgenceService\AgenceServiceIrium;

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
    public function mapStatutDemande(array $oldData, string $codeApplication): ?StatutDemande
    {
        if (empty($oldData['ID_Statut_Demande'])) {
            return null;
        }

        $descriptionStatut = $this->legacyDataFetcher->getStatutDemandeDescription($oldData['ID_Statut_Demande']);
        if ($descriptionStatut) {
            $statut = $this->em->getRepository(StatutDemande::class)
                ->findOneBy([
                    'codeApplication' => $codeApplication,
                    'description' => $descriptionStatut
                ]);
        }


        if (!$statut) {
            $this->logger->warning('StatutDemande non trouvé', [
                'ID_Statut_Demande' => $oldData['ID_Statut_Demande'],
                'Description_Statut' => $descriptionStatut ?? null,
            ]);
        }

        return $statut;
    }



    /**
     * Mappe une Agence
     * Stratégie : ID → Code (via legacy DB)
     */
    public function mapAgence(int $id): ?Agence
    {
        // Si pas trouvé, récupérer le code depuis l'ancienne base
        $codeAgence = $this->legacyDataFetcher->getAgenceCode($id);
        if ($codeAgence) {
            $agence = $this->em->getRepository(Agence::class)
                ->findOneBy(['code' => $codeAgence]);
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
    public function mapService(int $id): ?Service
    {

        $codeService = $this->legacyDataFetcher->getServiceCode($id);

        if ($codeService) {
            $service = $this->em->getRepository(Service::class)
                ->findOneBy(['code' => $codeService]);
        }


        if (!$service) {
            $this->logger->warning('Service non trouvé', ['id' => $id]);
        }

        return $service;
    }

    /**
     * Mappe un User par id
     */
    public function mapUserWithId(int $id): ?User
    {
        $username = $this->legacyDataFetcher->getUserName($id);
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (!$user) {
            $this->logger->warning('User non trouvé', ['id' => $id]);
        }

        return $user;
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

    /**
     * Mappe un agence service Irium
     */
    public function mapAgenceServiceIrium(int $id): ?AgenceServiceIrium
    {
        $agenceServiceIrium = null;
        $codeAgenceService = $this->legacyDataFetcher->getCodeAgenceService($id);

        if ($codeAgenceService) {
            $agenceServiceIrium = $this->em->getRepository(AgenceServiceIrium::class)
                ->findOneBy(['code' => $codeAgenceService]);
        }

        if (!$agenceServiceIrium) {
            $this->logger->warning('agenceServiceIrium non trouvé', ['old_id' => $id]);
        }

        return $agenceServiceIrium;
    }

    public function mapFullName(int $matricule): ?string
    {
        return $this->legacyDataFetcher->getFullName($matricule) ? $this->legacyDataFetcher->getFullName($matricule)[0]['fullname'] : null;
    }


    /**========================================================
     *  DEMANDE D'ORDRE DE MISSION DOM
     *=======================================================*/

    /**
     * Mappe la relation SousTypeDocument
     * Stratégie : ID → Code (via legacy DB)
     */
    public function mapSousTypeDocument(array $oldData): ?SousTypeDocument
    {
        if (empty($oldData['Sous_Type_Document'])) {
            return null;
        }

        // récupérer le code depuis l'ancienne base
        $codeSousType = $this->legacyDataFetcher->getSousTypeDocumentCode($oldData['Sous_Type_Document']);
        if ($codeSousType) {
            $sousType = $this->em->getRepository(SousTypeDocument::class)
                ->findOneBy(['codeSousType' => $codeSousType]);
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
        if (empty($oldData['Site'])) { // il existe déjà le nomZone du site dans l'ancien table
            return null;
        }


        // Si pas trouvé et qu'on a le champ Site (string), chercher par nomZone
        $site = $this->em->getRepository(Site::class)
            ->findOneBy(['nomZone' => $oldData['Site']]);

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
        if (empty($oldData['Categorie'])) { // il existe déjà le descritpion du categorie dans l'ancien table
            return null;
        }


        // Si pas trouvé et qu'on a le champ Categorie (string), chercher par description
        $categorie = $this->em->getRepository(Categorie::class)
            ->findOneBy(['description' => trim($oldData['Categorie'])]);

        if (!$categorie) {
            $this->logger->warning('Categorie non trouvée', [
                'category_id' => $oldData['category_id'],
                'Categorie' => $oldData['Categorie'] ?? null,
            ]);
        }

        return $categorie;
    }
}
