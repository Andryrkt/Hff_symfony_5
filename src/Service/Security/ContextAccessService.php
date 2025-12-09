<?php

namespace App\Service\Security;

use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\Historisation\TypeDocument;

class ContextAccessService
{
    /**
     * Retourne les agences/services autorisés pour un utilisateur DANS UNE TYPE DOCUMENT donnée.
     *
     * @param User              $user
     * @param TypeDocument|string $application  Soit l'entité TypeDocument, soit son code (ex: 'DOM')
     *
     * @return array{
     *     allAgences: bool,
     *     allServices: bool,
     *     agenceIds: int[]|null,
     *     serviceIds: int[]|null
     * }
     */
    private array $cache = [];

    /**
     * Retourne les agences/services autorisés pour un utilisateur DANS UNE TYPE DOCUMENT donnée.
     *
     * @param User              $user
     * @param TypeDocument|string $application  Soit l'entité TypeDocument, soit son code (ex: 'DOM')
     *
     * @return array{
     *     allAgences: bool,
     *     allServices: bool,
     *     agenceIds: int[]|null,
     *     serviceIds: int[]|null
     * }
     */
    public function getContextAccess(
        User $user,
        $application // peut être un Objet de type document ou le code de cette objet *on ne peut pas donner un type car on est dans php 7.4
    ): array {
        // Normalisation : si string → on récupère l'objet (ou null si invalide)
        if (is_string($application)) {
            $appCode = $application;
        } else {
            $appCode = $application->getTypeDocument();
        }

        // --- Memoization Key ---
        $cacheKey = $user->getId() . '_' . $appCode;
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        // 1. Admin = accès total partout
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return $this->cache[$cacheKey] = [
                'allAgences'  => true,
                'allServices' => true,
                'agenceIds'   => null,
                'serviceIds'  => null,
            ];
        }

        $hasAllAgences  = false;
        $hasAllServices = false;
        $agenceIds      = [];
        $serviceIds     = [];

        foreach ($user->getUserAccesses() as $access) {
            $accessApp = $access->getTypeDocument();

            // Cas 1 : accès global (application = null) → s'applique partout
            if ($accessApp === null) {
                $this->applyAccess($hasAllAgences, $hasAllServices, $agenceIds, $serviceIds, $access);
                continue;
            }

            // Cas 2 : accès spécifique à l'application demandée
            if ($accessApp->getTypeDocument() === $appCode) {
                $this->applyAccess($hasAllAgences, $hasAllServices, $agenceIds, $serviceIds, $access);
            }
            // Sinon → accès ignoré pour cette application
        }

        return $this->cache[$cacheKey] = [
            'allAgences'  => $hasAllAgences,
            'allServices' => $hasAllServices,
            'agenceIds'   => $hasAllAgences ? null : array_unique($agenceIds),
            'serviceIds'  => $hasAllServices ? null : array_unique($serviceIds),
        ];
    }

    /**
     * Applique un UserAccess aux variables de résultat
     */
    private function applyAccess(
        bool &$hasAllAgences,
        bool &$hasAllServices,
        array &$agenceIds,
        array &$serviceIds,
        $access
    ): void {
        if ($access->getAllAgence()) {
            $hasAllAgences = true;
        } elseif ($access->getAgence()) {
            $agenceIds[] = $access->getAgence()->getId();
        }

        if ($access->getAllService()) {
            $hasAllServices = true;
        } elseif ($access->getService()) {
            $serviceIds[] = $access->getService()->getId();
        }
    }

    // ————————————————————————
    // Méthodes utilitaires rapides
    // ————————————————————————

    public function hasFullAccess(User $user, mixed $application): bool
    {
        $access = $this->getContextAccess($user, $application);
        return $access['allAgences'] && $access['allServices'];
    }

    public function canAccessAgence(User $user, mixed $application, int $agenceId): bool
    {
        $access = $this->getContextAccess($user, $application);
        if ($access['allAgences']) {
            return true;
        }
        return in_array($agenceId, $access['agenceIds'] ?? [], true);
    }

    public function canAccessService(User $user, mixed $application, int $serviceId): bool
    {
        $access = $this->getContextAccess($user, $application);
        if ($access['allServices']) {
            return true;
        }
        return in_array($serviceId, $access['serviceIds'] ?? [], true);
    }
}
