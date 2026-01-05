<?php

namespace App\Service\Traits;

trait ReportingSubmenuTrait
{
    protected function reportingSubmenu(): array
    {
        return [
            // ======== Reporting Power BI ========== 
            [
                'label' => 'Reporting Power BI',
                'route' => '#'
            ],
            // ======== Reporting Excel ========== 
            [
                'label' => 'Reporting Excel',
                'route' => '#'
            ]
        ];
    }
}
