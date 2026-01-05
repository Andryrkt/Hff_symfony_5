<?php

namespace App\Service\Traits;

trait ItSubmenuTrait
{
    protected function itSubmenu(): array
    {
        return [
            // ======== Nouvelle demande ========== 
            [
                'label' => 'Nouvelle demande',
                'icon' => 'fas fa-plus',
                'route' => '#',
            ],
            // ======== Consultation ========== 
            [
                'label' => 'Consultation',
                'icon' => 'fas fa-search',
                'route' => '#',
            ],
            // ======== Planning ========== 
            [
                'label' => 'Planning',
                'icon' => 'fas fa-calendar-alt',
                'route' => '#',
            ],
        ];
    }
}
