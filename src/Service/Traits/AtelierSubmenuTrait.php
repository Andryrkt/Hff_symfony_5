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
                'icon' => 'fas fa-tools',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'icon' => 'fas fa-plus', 'route' => '#'],
                    ['label' => 'Consultation', 'icon' => 'fas fa-search', 'route' => '#']
                ]
            ],
            // ======== Glossaire OR ========== 
            [
                'label' => 'Glossaire OR',
                'icon' => 'fas fa-book',
                'route' => '#',
            ],
            // ======== Planning  ========== 
            [
                'label' => 'Planning',
                'icon' => 'fas fa-calendar-alt',
                'route' => '#',
            ],
            // ======== Planning détaillé ========== 
            [
                'label' => 'Planning détaillé',
                'icon' => 'fas fa-calendar-week',
                'route' => '#',
            ],
            // ======== Planning interne Atelier ========== 
            [
                'label' => 'Planning interne Atelier',
                'icon' => 'fas fa-calendar-check',
                'route' => '#',
            ],
            // ======== Satisfaction client (Atelier excellence survey) ========== 
            [
                'label' => 'Satisfaction client (Atelier excellence survey)',
                'icon' => 'fas fa-smile',
                'route' => '#',
            ]
        ];
    }
}
