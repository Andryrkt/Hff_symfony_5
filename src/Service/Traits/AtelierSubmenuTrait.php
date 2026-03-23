<?php

namespace App\Service\Traits;

trait AtelierSubmenuTrait
{
    protected function ditSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'route' => 'hf_atelier_dit_form_index', 'icon' => 'fas fa-plus'],
            ['label' => 'Consultation', 'route' => 'hf_atelier_dit_liste_index', 'icon' => 'fas fa-search']
        ];
    }

    protected function atelierSubmenu(): array
    {
        return [
            // ======== Demande d'intervention ========== 
            [
                'label' => 'Demande d\'intervention',
                'icon' => 'fas fa-tools',
                'route' => null,
                'submenu' => $this->ditSubmenu()
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
