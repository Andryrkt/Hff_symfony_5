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
                'route' => '#',
            ],
            // ======== Documentation ========== 
            [
                'label' => 'Documentation',
                'route' => '#',
            ]
        ];
    }
}
