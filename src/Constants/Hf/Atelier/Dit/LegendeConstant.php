<?php

namespace App\Constants\Hf\Atelier\Dit;

final class LegendeConstant
{
    public const LEGENDE_DIT = [
        [
            'color' => 'info',
            'label' => 'Partiellement dispo',
            'icon' => 'fa-circle'
        ],
        [
            'color' => 'primary',
            'label' => 'Complet non livré',
            'icon' => 'fa-circle'
        ],
        [
            'color' => 'warning',
            'label' => 'Partiellement livré',
            'icon' => 'fa-circle'
        ],
        [
            'color' => 'success',
            'label' => 'Tout livré',
            'icon' => 'fa-circle'
        ],
    ];
}
