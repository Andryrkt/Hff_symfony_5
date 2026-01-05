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
                'route' => '#',
            ],
            // ======== Consultation ========== 
            [
                'label' => 'Consultation',
                'route' => '#',
            ],
            // ======== Planning ========== 
            [
                'label' => 'Planning',
                'route' => '#',
            ],
        ];
    }
}
