<?php

namespace App\Service\Traits;

trait PolSubmenuTrait
{
    protected function polSubmenu(): array
    {
        return [
            // ======== Nouvelle DLUB ========== 
            [
                'label' => 'Nouvelle DLUB',
                'route' => '#',
            ],
            // ======== Consultation DLUB ========== 
            [
                'label' => 'Consultation DLUB',
                'route' => '#',
            ],
            // ======== Liste des commandes fournisseur ========== 
            [
                'label' => 'Liste des commandes fournisseur',
                'route' => '#',
            ],
            // ======== Pneumatiques ========== 
            [
                'label' => 'Pneumatiques',
                'route' => '#',
            ],
        ];
    }
}
