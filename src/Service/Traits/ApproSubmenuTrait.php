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
                'route' => '#',
            ],
            // ======== Consultation des DA ========== 
            [
                'label' => 'Consultation des DA',
                'route' => '#',
            ],
            // ======== Liste des commandes fournisseurs ========== 
            [
                'label' => 'Liste des commandes fournisseurs',
                'route' => '#',
            ],
        ];
    }
}
