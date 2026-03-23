<?php

namespace App\Service\Traits;

trait HseSubmenuTrait
{
    protected function hseSubmenu(): array
    {
        return [
            // ======== Rapport d'incident ========== 
            [
                'label' => 'Rapport d\'incident',
                'icon' => 'fas fa-exclamation-triangle',
                'route' => '#',
            ],
            // ======== Documentation ========== 
            [
                'label' => 'Documentation',
                'icon' => 'fas fa-book',
                'route' => '#',
            ]
        ];
    }
}
