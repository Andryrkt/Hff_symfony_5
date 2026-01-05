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
                'icon' => 'fas fa-chart-pie',
                'route' => '#'
            ],
            // ======== Reporting Excel ========== 
            [
                'label' => 'Reporting Excel',
                'icon' => 'fas fa-file-excel',
                'route' => '#'
            ]
        ];
    }
}
