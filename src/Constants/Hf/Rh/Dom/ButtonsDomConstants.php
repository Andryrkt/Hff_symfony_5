<?php

namespace App\Constants\Hf\Rh\Dom;

final class ButtonsDomConstants
{
    public const BUTTONS_ACTIONS = [
        [
            'href' => 'dom_first_form',
            'class' => 'bg-warning',
            'text' => 'Nouvelle demande',
            'tooltip' => 'Nouvelle demande'
        ],
        [
            'href' => 'dom_export_excel',
            'class' => 'bg-success',
            'icon' => 'fas fa-table',
            'text' => 'Excel',
            'tooltip' => 'Exporter en Excel'
        ],
    ];
}
