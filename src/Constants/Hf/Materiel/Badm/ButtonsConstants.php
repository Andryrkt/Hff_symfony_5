<?php

namespace App\Constants\Hf\Materiel\Badm;

final class ButtonsConstants
{
    public const BUTTONS_ACTIONS = [
        [
            'href' => 'hf_materiel_badm_first_form_index',
            'class' => 'bg-warning',
            'text' => 'Nouvelle demande',
            'tooltip' => 'Nouvelle demande'
        ],
        [
            'href' => 'hf_materiel_badm_export_excel_index',
            'class' => 'bg-success',
            'icon' => 'fas fa-table',
            'text' => 'Excel',
            'tooltip' => 'Exporter en Excel'
        ],
    ];
}
