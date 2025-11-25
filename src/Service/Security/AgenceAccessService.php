<?php

namespace App\Service\Security;

use App\Entity\Admin\PersonnelUser\User;

/**
 * Service pour gérer l'accès aux agences selon les permissions de l'utilisateur.
 * 
 * Ce service centralise la logique qui détermine quelles agences un utilisateur
 * peut visualiser en fonction de son rôle et de ses UserAccess.
 */
class AgenceAccessService
{
    /**
     * Retourne les IDs des agences autorisées pour un utilisateur.
     * 
     * @param User $user L'utilisateur connecté
     * @return array|null 
     *   - null si l'utilisateur a accès à toutes les agences (admin ou accès total)
     *   - array d'IDs d'agences sinon
     */
    public function getAuthorizedAgenceIds(User $user): ?array
    {
        // 1️⃣ Si l'utilisateur est admin => accès à toutes les agences
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return null;
        }

        $agenceIds = [];

        // 2️⃣ Parcourir les UserAccess pour déterminer les agences autorisées
        foreach ($user->getUserAccesses() as $access) {
            // Si l'utilisateur a un accès à toutes les agences
            if ($access->getAllAgence()) {
                return null; // Accès complet
            }

            // Si l'utilisateur a accès à une agence spécifique
            if ($access->getAgence()) {
                $agenceIds[] = $access->getAgence()->getId();
            }
        }

        // 3️⃣ Retourner les IDs uniques (éviter les doublons)
        return array_unique($agenceIds);
    }

    /**
     * Vérifie si un utilisateur a accès à toutes les agences.
     * 
     * @param User $user L'utilisateur à vérifier
     * @return bool True si l'utilisateur a accès à toutes les agences
     */
    public function hasAccessToAllAgences(User $user): bool
    {
        return $this->getAuthorizedAgenceIds($user) === null;
    }

    /**
     * Vérifie si un utilisateur a accès à une agence spécifique.
     * 
     * @param User $user L'utilisateur à vérifier
     * @param int $agenceId L'ID de l'agence
     * @return bool True si l'utilisateur a accès à cette agence
     */
    public function hasAccessToAgence(User $user, int $agenceId): bool
    {
        $authorizedIds = $this->getAuthorizedAgenceIds($user);

        // Si null => accès à toutes les agences
        if ($authorizedIds === null) {
            return true;
        }

        // Vérifier si l'agence est dans la liste autorisée
        return in_array($agenceId, $authorizedIds, true);
    }
}
