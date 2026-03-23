<?php

namespace App\Service\Traits;

trait ApproSubmenuTrait
{
    protected function approSubmenu(): array
    {
        return [
            // ======== Nouvelle DA ========== 
            [
                'label' => 'Nouvelle DA',
                'icon' => 'fas fa-plus',
                'route' => '#',
            ],
            // ======== Consultation des DA ========== 
            [
                'label' => 'Consultation des DA',
                'icon' => 'fas fa-search',
                'route' => '#',
            ],
            // ======== Liste des commandes fournisseurs ========== 
            [
                'label' => 'Liste des commandes fournisseurs',
                'icon' => 'fas fa-list',
                'route' => '#',
            ],
        ];
    }
}
