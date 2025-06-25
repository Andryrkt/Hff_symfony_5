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

        return [
            [
                'label' => 'Accueil',
                'route' => 'app_home',
                'icon' => 'fas fa-home',
                'visible' => true,
            ],
            [
                'label' => 'Connexion',
                'route' => 'app_login',
                'icon' => 'fas fa-sign-in-alt',
                'visible' => !$isLoggedIn,
            ],
            // [
            //     'label' => 'Paramètre',
            //     'icon' => 'fas fa-briefcase',
            //     'visible' => $isLoggedIn,
            //     'children' => [
            //         [
            //             'label' => 'user',
            //             'route' => '#',
            //             'icon' => 'fas fa-plus-circle',
            //             'visible' => true,
            //         ],
            //         [
            //             'label' => 'Consultation',
            //             'route' => '#',
            //             'icon' => 'fas fa-list',
            //             'visible' => true,
            //         ],
            //         ]
            //     ],
        ];
    }
}
