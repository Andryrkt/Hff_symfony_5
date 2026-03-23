<?php

namespace App\Service\Traits;

trait ComptaSubmenuTrait
{
    protected function comptaSubmenu(): array
    {
        return [
            // ======== Cours de change ========== 
            [
                'label' => 'Cours de change',
                'icon' => 'fas fa-money-bill-wave',
                'route' => '#'
            ],
            // ======== Demande de paiement ========== 
            [
                'label' => 'Demande de paiement',
                'icon' => 'fas fa-file-invoice-dollar',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'icon' => 'fas fa-plus', 'route' => '#'],
                    ['label' => 'Consultation', 'icon' => 'fas fa-search', 'route' => '#']
                ]
            ],
            // ======== Bon de caisse ========== 
            [
                'label' => 'Bon de caisse',
                'icon' => 'fas fa-receipt',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'icon' => 'fas fa-plus', 'route' => '#'],
                    ['label' => 'Consultation', 'icon' => 'fas fa-search', 'route' => '#']
                ]
            ]
        ];
    }
}
