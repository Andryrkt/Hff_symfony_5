<?php

namespace App\Service\Traits;

trait AtelierSubmenuTrait
{
    protected function atelierSubmenu(): array
    {
        return [
            // ======== Demande d'intervention ========== 
            [
                'label' => 'Demande d\'intervention',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'route' => '#'],
                    ['label' => 'Consultation', 'route' => '#']
                ]
            ],
            // ======== Glossaire OR ========== 
            [
                'label' => 'Glossaire OR',
                'route' => '#',
            ],
            // ======== Planning  ========== 
            [
                'label' => 'Planning',
                'route' => '#',
            ],
            // ======== Planning détaillé ========== 
            [
                'label' => 'Planning détaillé',
                'route' => '#',
            ],
            // ======== Planning interne Atelier ========== 
            [
                'label' => 'Planning interne Atelier',
                'route' => '#',
            ],
            // ======== Satisfaction client (Atelier excellence survey) ========== 
            [
                'label' => 'Satisfaction client (Atelier excellence survey)',
                'route' => '#',
            ]
        ];
    }
}
