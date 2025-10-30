<?php

namespace App\Security\Voter;


use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use App\Entity\Admin\PersonnelUser\UserAccess;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Voter chargé de vérifier si un utilisateur peut accéder à une ressource
 * selon le contexte d’agence et de service.
 *
 * Exemple :
 *   $this->isGranted('CONTEXT_ACCESS', [$agence, $service])
 */
class ContextVoter extends Voter
{
    public const ACCESS = 'CONTEXT_ACCESS';

    protected function supports(string $attribute, $subject): bool
    {
        // Le voter ne gère que l'attribut CONTEXT_ACCESS
        if ($attribute !== self::ACCESS) {
            return false;
        }

        // Le sujet doit être un tableau [agence, service]
        return is_array($subject) && count($subject) === 2;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Agence|null $agence */
        /** @var Service|null $service */
        [$agence, $service] = $subject;

        // 1️⃣ Si l'utilisateur est admin => accès total
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // 2️⃣ Vérifie les accès étendus
        foreach ($user->getUserAccesses() as $access) {
            if ($this->matchAccess($access, $agence, $service)) {
                return true;
            }
        }

        // 3️⃣ Par défaut, non autorisé
        return false;
    }

    private function matchAccess(UserAccess $access, ?Agence $agence, ?Service $service): bool
    {
        // Cas 1️⃣ : Accès total
        if ($access->getAllAgence() && $access->getAllService()) {
            return true;
        }

        // Cas 2️⃣ : Accès à toutes les agences pour un service précis
        if ($access->getAllAgence() && $access->getService()) {
            return $service && $access->getService()->getId() === $service->getId();
        }

        // Cas 3️⃣ : Accès à tous les services d'une agence précise
        if ($access->getAllService() && $access->getAgence()) {
            return $agence && $access->getAgence()->getId() === $agence->getId();
        }

        // Cas 4️⃣ : Accès restreint (agence + service)
        if ($access->getAgence() && $access->getService()) {
            return (
                $agence && $access->getAgence()->getId() === $agence->getId() &&
                $service && $access->getService()->getId() === $service->getId()
            );
        }

        return false;
    }
}
