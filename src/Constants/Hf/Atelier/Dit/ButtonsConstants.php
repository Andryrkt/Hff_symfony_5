<?php

namespace App\Constants\Hf\Atelier\Dit;

final class ButtonsConstants
{
    public const BUTTONS_ACTIONS = [
        [
            'href' => 'hf_atelier_dit_form_index',
            'class' => 'bg-warning',
            'text' => 'Nouvelle demande',
            'tooltip' => 'Nouvelle demande'
        ],
        [
            'href' => 'hf_atelier_dit_export_excel_index',
            'class' => 'bg-success',
            'icon' => 'fas fa-table',
            'text' => 'Excel',
            'tooltip' => 'Exporter en Excel'
        ],
    ];
}
