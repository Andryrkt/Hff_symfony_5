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
                'icon' => 'fas fa-plus',
                'route' => '#',
            ],
            // ======== Consultation DLUB ========== 
            [
                'label' => 'Consultation DLUB',
                'icon' => 'fas fa-search',
                'route' => '#',
            ],
            // ======== Liste des commandes fournisseur ========== 
            [
                'label' => 'Liste des commandes fournisseur',
                'icon' => 'fas fa-list',
                'route' => '#',
            ],
            // ======== Pneumatiques ========== 
            [
                'label' => 'Pneumatiques',
                'icon' => 'fas fa-dot-circle',
                'route' => '#',
            ],
        ];
    }
}
