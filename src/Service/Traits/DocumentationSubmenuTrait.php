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
                'icon' => 'fas fa-address-book',
                'route' => '#'
            ],
            // ======== Plan analytique Hff ========== 
            [
                'label' => 'Plan analytique Hff',
                'icon' => 'fas fa-sitemap',
                'route' => '#'
            ],
            // ======== Documentation interne ========== 
            [
                'label' => 'Documentation interne',
                'icon' => 'fas fa-book',
                'route' => '#'
            ],
            // ======== Contrat ========== 
            [
                'label' => 'Contrat',
                'icon' => 'fas fa-file-contract',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle contrat', 'icon' => 'fas fa-plus', 'route' => '#'],
                    ['label' => 'Consultation', 'icon' => 'fas fa-search', 'route' => '#']
                ]
            ]
        ];
    }
}
