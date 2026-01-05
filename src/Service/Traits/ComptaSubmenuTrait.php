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
                'route' => '#'
            ],
            // ======== Demande de paiement ========== 
            [
                'label' => 'Demande de paiement',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'route' => '#'],
                    ['label' => 'Consultation', 'route' => '#']
                ]
            ],
            // ======== Bon de caisse ========== 
            [
                'label' => 'Bon de caisse',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'route' => '#'],
                    ['label' => 'Consultation', 'route' => '#']
                ]
            ]
        ];
    }
}
