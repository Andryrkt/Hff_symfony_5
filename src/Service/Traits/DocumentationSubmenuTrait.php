<?php

namespace App\Service\Traits;

trait DocumentationSubmenuTrait
{
    protected function documentationSubmenu(): array
    {
        return [
            // ======== Annuaire ==========
            [
                'label' => 'Annuaire',
                'route' => '#'
            ],
            // ======== Plan analytique Hff ========== 
            [
                'label' => 'Plan analytique Hff',
                'route' => '#'
            ],
            // ======== Documentation interne ========== 
            [
                'label' => 'Documentation interne',
                'route' => '#'
            ],
            // ======== Contrat ========== 
            [
                'label' => 'Contrat',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle contrat', 'route' => '#'],
                    ['label' => 'Consultation', 'route' => '#']
                ]
            ]
        ];
    }
}
