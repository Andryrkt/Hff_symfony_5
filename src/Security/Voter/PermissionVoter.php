<?php

namespace App\Security\Voter;


use App\Entity\Admin\ApplicationGroupe\Permission;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\UserAccess;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PermissionVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        // Ce voter s’applique à toute permission sous forme de code (string)
        return is_string($attribute);
    }

    /**
     * @param string $attribute  -> ex: "RH_CONGE_CREATE"
     * @param mixed $subject     -> peut être une entité (ex: une demande de congé) ou null
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // 1️⃣ Vérifie si l’utilisateur a la permission en direct
        $permissionCodes = $user->getPermissionsDirectes()->map(fn(Permission $p) => $p->getCode())->toArray();
        if (in_array($attribute, $permissionCodes, true)) {
            return true;
        }

        // 2️⃣ Vérifie via les accès étendus (UserAccess)
        foreach ($user->getUserAccesses() as $access) {
            if ($this->hasPermissionInAccess($access, $attribute)) {
                return true;
            }
        }

        // 3️⃣ Vérifie éventuellement les rôles globaux (ADMIN, CHEF_SERVICE, etc.)
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // Par défaut, refus
        return false;
    }

    private function hasPermissionInAccess(UserAccess $access, string $permission): bool
    {
        $permissionCodes = $access->getPermissions()->map(fn(Permission $p) => $p->getCode())->toArray();
        // Si toutes les agences et tous les services sont autorisés
        if ($access->getAllAgence() && $access->getAllService()) {
            return in_array($permission, $permissionCodes, true);
        }

        // Tu pourrais affiner ici selon agence/service courant (si $subject contient une info)
        return in_array($permission, $permissionCodes, true);
    }
}
