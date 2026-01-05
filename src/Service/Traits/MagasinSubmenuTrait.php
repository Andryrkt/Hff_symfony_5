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
                'route' => null,
                'submenu' => [
                    ['label' => 'Liste à traiter', 'route' => '#'],
                    ['label' => 'Liste à livrer', 'route' => '#']
                ]
            ],
            // ======== CIS ========== 
            [
                'label' => 'CIS',
                'route' => null,
                'submenu' => [
                    ['label' => 'Liste à traiter', 'route' => '#'],
                    ['label' => 'Liste à livrer', 'route' => '#']
                ]
            ],
            // ======== INVENTAIRE ========== 
            [
                'label' => 'INVENTAIRE',
                'route' => null,
                'submenu' => [
                    ['label' => 'Liste inventaire', 'route' => '#'],
                    ['label' => 'Inventaire détaillé', 'route' => '#']
                ]
            ],
            // ======== SORTIE DE PIECES ========== 
            [
                'label' => 'SORTIE DE PIECES',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'route' => '#']
                ]
            ],
            // ======== DEMATERIALISATION ========== 
            [
                'label' => 'OR',
                'route' => null,
                'submenu' => [
                    ['label' => 'Devis', 'route' => '#'],
                    ['label' => 'Commandes clients', 'route' => '#'],
                    ['label' => 'Planning magasin', 'route' => '#']
                ]
            ],
            // ======== Soumission commandes fournisseurs ========== 
            [
                'label' => 'Soumission commandes fournisseurs',
                'route' => '#',
            ],
            // ======== Liste des non placées ========== 
            [
                'label' => 'Liste des non placées',
                'route' => '#',
            ]
        ];
    }
}
