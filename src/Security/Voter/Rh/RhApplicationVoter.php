<?php

namespace App\Security\Voter;

use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\ApplicationGroupe\Application;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RhApplicationVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // Attributs spécifiques à l'application RH
        return in_array($attribute, ['VALIDER_CONGE', 'VOIR_FICHE_PAIE'])
            && $subject instanceof Application
            && $subject->getName() === 'RH';
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        // Exemple de logique d'accès RH
        // Ici tu ajoutes ta logique métier RH
        if ($attribute === 'VALIDER_CONGE') {
            // Exemple : vérifier si l'utilisateur a le droit de valider les congés
            // return true ou false selon ta logique
            return in_array('ROLE_RH_MANAGER', $user->getRoles());
        }
        if ($attribute === 'VOIR_FICHE_PAIE') {
            // Exemple : vérifier si l'utilisateur a le droit de voir les fiches de paie
            return in_array('ROLE_RH', $user->getRoles());
        }
        return false;
    }
} 