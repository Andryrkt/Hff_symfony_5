<?php

namespace App\Security\Voter;


use App\Entity\Admin\PersonnelUser\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ObjectVoter extends Voter
{
    private AuthorizationCheckerInterface $authChecker;

    public function __construct(
        AuthorizationCheckerInterface $authChecker
    ) {
        $this->authChecker = $authChecker;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // On ne gère que des entités (objets métiers)
        return is_object($subject) && in_array($attribute, [
            'VIEW',
            'EDIT',
            'DELETE',
            'VALIDATE',
            'CREATE',
        ]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        // Exemple de mapping permission par type d'objet
        $map = [
            // DemandeConge::class => 'RH_CONGE_',
            // BonCommande::class => 'APPRO_BC_',
            // tu peux ajouter d'autres entités ici
        ];

        $class = get_class($subject);
        if (!isset($map[$class])) {
            return false; // objet non pris en charge
        }

        $prefix = $map[$class];
        $permissionCode = $prefix . strtoupper($attribute); // ex : RH_CONGE_VIEW

        // 1️⃣ Vérifie la permission métier
        if (!$this->authChecker->isGranted($permissionCode)) {
            return false;
        }

        // 2️⃣ Vérifie le contexte agence/service
        //    (si l'entité contient des relations d'organisation)
        $agence = method_exists($subject, 'getAgence') ? $subject->getAgence() : null;
        $service = method_exists($subject, 'getService') ? $subject->getService() : null;

        if ($agence || $service) {
            if (!$this->authChecker->isGranted('CONTEXT_ACCESS', [$agence, $service])) {
                return false;
            }
        }

        // 3️⃣ Cas particulier : si l’utilisateur est créateur du document,
        // il a toujours le droit de "VIEW" (à adapter selon ton besoin)
        if ($attribute === 'VIEW' && method_exists($subject, 'getCreatedBy')) {
            if ($subject->getCreatedBy() === $user) {
                return true;
            }
        }

        return true;
    }
}
