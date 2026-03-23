<?php

namespace App\Service\Traits;

trait EnergieSubmenuTrait
{
    protected function energieSubmenu(): array
    {
        return [
            // ======== Rapport de production centrale ========== 
            [
                'label' => 'Rapport de production centrale',
                'icon' => 'fas fa-bolt',
                'route' => '#',
            ]
        ];
    }
}
