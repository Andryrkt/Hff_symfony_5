<?php

namespace App\Security\Voter;

use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Entity\Admin\ApplicationGroupe\Vignette;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class VignetteVoter extends Voter
{
    /**
     * Actions supportées par ce voter.
     * Exemple : voir ou accéder à une application/vignette.
     */
    public const ACCESS = 'APPLICATION_ACCESS';

    protected function supports(string $attribute, $subject): bool
    {
        // On ne gère que l’accès aux vignettes
        return $attribute === self::ACCESS && $subject instanceof Vignette;
    }

    /**
     * Logique d’autorisation
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false; // utilisateur non connecté
        }

        // Super admin → accès total
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        /** @var Vignette $vignette */
        $vignette = $subject;

        switch ($attribute) {
            case self::ACCESS:
                return $this->canAccessVignette($user, $vignette);
        }

        return false;
    }

    /**
     * Vérifie si le user a accès à une vignette donnée.
     */
    private function canAccessVignette(User $user, Vignette $vignette): bool
    {
        // 1️⃣ Si l'utilisateur a des accès directs (UserAccess) liés à cette vignette
        foreach ($user->getUserAccesses() as $access) {
            if (!$access instanceof UserAccess) {
                continue;
            }

            // On peut considérer que chaque UserAccess contient des permissions (array de codes)
            foreach ($access->getPermissions() as $permCode) {
                // Si la permission commence par le code de vignette, on considère qu’il a accès
                // Exemple : "RH_" → RH_CONGE_VIEW, RH_CONGE_CREATE, etc.
                if (str_starts_with($permCode->getCode(), strtoupper($vignette->getNom() . '_'))) {
                    return true;
                }
            }
        }

        // 2️⃣ Sinon, vérifier les permissions directes du User (cas simple)
        foreach ($user->getPermissionsDirectes() as $permCode) {
            if (str_starts_with($permCode->getCode(), strtoupper($vignette->getNom() . '_'))) {
                return true;
            }
        }

        // 3️⃣ Sinon, pas d'accès
        return false;
    }
}
