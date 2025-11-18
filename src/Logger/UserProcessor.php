<?php

namespace App\Logger;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProcessor
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(array $record): array
    {
        $user = $this->security->getUser();

        if ($user instanceof UserInterface) {
            // getUserIdentifier() est la méthode moderne, getUsername() est pour la compatibilité
            if (method_exists($user, 'getUserIdentifier')) {
                $identifier = $user->getUserIdentifier();
            } else {
                $identifier = $user->getUsername();
            }
            $record['extra']['user_email'] = $identifier;
        } else {
            $record['extra']['user_email'] = 'anonymous';
        }

        return $record;
    }
}
