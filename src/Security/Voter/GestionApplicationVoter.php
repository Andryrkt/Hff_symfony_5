<?php

namespace App\Security\Voter;

use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\ApplicationGroupe\Application;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GestionApplicationVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // Attributs spécifiques à l'application Gestion
        return in_array($attribute, ['EXPORTER_RAPPORT', 'SUPPRIMER_FACTURE'])
            && $subject instanceof Application
            && $subject->getName() === 'GESTION';
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        // Exemple de logique d'accès Gestion
        if ($attribute === 'EXPORTER_RAPPORT') {
            // Exemple : vérifier si l'utilisateur a le droit d'exporter les rapports
            return in_array('ROLE_GESTION', $user->getRoles());
        }
        if ($attribute === 'SUPPRIMER_FACTURE') {
            // Exemple : vérifier si l'utilisateur a le droit de supprimer une facture
            return in_array('ROLE_GESTION_ADMIN', $user->getRoles());
        }
        return false;
    }
} 