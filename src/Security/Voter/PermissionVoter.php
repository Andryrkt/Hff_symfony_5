<?php

namespace App\Security\Voter;

use App\Contract\AgencyServiceAwareInterface;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\UserAccess;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface as CoreUserInterface;

class PermissionVoter extends Voter
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['CREATE', 'READ', 'SUBMIT'])
            && ($subject === null || $subject instanceof AgencyServiceAwareInterface);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Si l'utilisateur a le rôle ROLE_ADMIN, il a toutes les permissions
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Pour les actions de création (subject est null), nous devons définir une logique spécifique.
        // Si vous voulez permettre la création sans agence/service spécifique, la logique doit être ici.
        // Pour l'instant, si le sujet est null, nous refusons l'accès car le voter est basé sur agence/service de l'objet.
        if ($subject === null) {
            return false;
        }

        /** @var AgencyServiceAwareInterface $subject */
        $emitterAgence = $subject->getEmitterAgence();
        $emitterService = $subject->getEmitterService();
        $debtorAgence = $subject->getDebtorAgence();
        $debtorService = $subject->getDebtorService();

        // Si l'une des agences ou services n'est pas définie, on ne peut pas vérifier la permission
        if (!$emitterAgence || !$emitterService || !$debtorAgence || !$debtorService) {
            return false;
        }

        // Vérifier la permission pour l'agence/service émetteur
        $emitterUserAccess = $this->entityManager->getRepository(UserAccess::class)
            ->findOneBy([
                'user' => $user,
                'agence' => $emitterAgence,
                'service' => $emitterService
            ]);

        if (!$emitterUserAccess || !in_array($attribute, $emitterUserAccess->getPermissions())) {
            return false; // Pas de permission pour l'émetteur
        }

        // Vérifier la permission pour l'agence/service débiteur
        $debtorUserAccess = $this->entityManager->getRepository(UserAccess::class)
            ->findOneBy([
                'user' => $user,
                'agence' => $debtorAgence,
                'service' => $debtorService
            ]);

        if (!$debtorUserAccess || !in_array($attribute, $debtorUserAccess->getPermissions())) {
            return false; // Pas de permission pour le débiteur
        }

        // Si les deux vérifications passent, l'utilisateur a la permission
        return true;
    }
}
