<?php

namespace App\Service\Traits;

trait MagasinSubmenuTrait
{
    protected function magasinSubmenu(): array
    {
        return [
            // ======== OR ========== 
            [
                'label' => 'OR',
                'icon' => 'fas fa-tools',
                'route' => null,
                'submenu' => [
                    ['label' => 'Liste à traiter', 'icon' => 'fas fa-search', 'route' => '#'],
                    ['label' => 'Liste à livrer', 'icon' => 'fas fa-truck-loading', 'route' => '#']
                ]
            ],
            // ======== CIS ========== 
            [
                'label' => 'CIS',
                'icon' => 'fas fa-warehouse',
                'route' => null,
                'submenu' => [
                    ['label' => 'Liste à traiter', 'icon' => 'fas fa-search', 'route' => '#'],
                    ['label' => 'Liste à livrer', 'icon' => 'fas fa-truck-loading', 'route' => '#']
                ]
            ],
            // ======== INVENTAIRE ========== 
            [
                'label' => 'INVENTAIRE',
                'icon' => 'fas fa-clipboard-list',
                'route' => null,
                'submenu' => [
                    ['label' => 'Liste inventaire', 'icon' => 'fas fa-search', 'route' => '#'],
                    ['label' => 'Inventaire détaillé', 'icon' => 'fas fa-list-alt', 'route' => '#']
                ]
            ],
            // ======== SORTIE DE PIECES ========== 
            [
                'label' => 'SORTIE DE PIECES',
                'icon' => 'fas fa-dolly',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'icon' => 'fas fa-plus', 'route' => '#']
                ]
            ],
            // ======== DEMATERIALISATION ========== 
            [
                'label' => 'DEMATERIALISATION',
                'icon' => 'fas fa-laptop-code',
                'route' => null,
                'submenu' => [
                    ['label' => 'Devis', 'icon' => 'fas fa-file-invoice', 'route' => '#'],
                    ['label' => 'Commandes clients', 'icon' => 'fas fa-users', 'route' => '#'],
                    ['label' => 'Planning magasin', 'icon' => 'fas fa-calendar-alt', 'route' => '#']
                ]
            ],
            // ======== Soumission commandes fournisseurs ========== 
            [
                'label' => 'Soumission commandes fournisseurs',
                'icon' => 'fas fa-paper-plane',
                'route' => '#',
            ],
            // ======== Liste des non placées ========== 
            [
                'label' => 'Liste des non placées',
                'icon' => 'fas fa-times-circle',
                'route' => '#',
            ]
        ];
    }
}
