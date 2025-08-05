<?php

namespace App\Service\Menu;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuBuilder
{
    private $tokenStorage;
    private $authChecker;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authChecker = $authChecker;
    }

    public function getMainMenu(): array
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        $isLoggedIn = $this->authChecker->isGranted('IS_AUTHENTICATED_FULLY');

        $menu = [
            [
                'label' => 'Connexion',
                'route' => 'app_login',
                'icon' => 'fas fa-sign-in-alt',
                'visible' => !$isLoggedIn,
            ]
        ];

        if ($isLoggedIn && $user !== null) {
            $menu[] = [
                'label' => 'Dematerialisations',
                'icon' => 'fa-solid fa-file',
                'visible' => true,
                'children' => [
                    [
                        'label' => 'Nouvelle Demande',
                        'route' => 'dom_first',
                        'icon' => 'fas fa-plus',
                        'visible' => true,
                    ],
                ],
            ];
            $menu[] = [
                'label' => $user->getUserIdentifier(),
                'icon' => 'fa-solid fa-user-astronaut',
                'visible' => true,
                'children' => [
                    [
                        'label' => 'Déconnexion',
                        'route' => 'app_logout',
                        'icon' => 'fas fa-sign-out-alt',
                        'visible' => true,
                    ],
                ],
            ];
        }

        return $menu;
    }
}
