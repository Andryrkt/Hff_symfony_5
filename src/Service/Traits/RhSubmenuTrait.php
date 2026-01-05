<?php

namespace App\Service\Traits;

trait RhSubmenuTrait
{
    /**>>>--------------  RH  -------------------*/
    protected function domSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'icon' => 'fas fa-plus', 'route' => 'dom_first_form'],
            ['label' => 'Consultation', 'icon' => 'fas fa-search', 'route' => 'dom_liste_index']
        ];
    }

    protected function mutationSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'icon' => 'fas fa-plus', 'route' => '#'],
            ['label' => 'Consultation', 'icon' => 'fas fa-search', 'route' => '#']
        ];
    }

    protected function congeSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'icon' => 'fas fa-plus', 'route' => '#'],
            ['label' => 'Consultation', 'icon' => 'fas fa-search', 'route' => '#']
        ];
    }

    protected function temporaireSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'icon' => 'fas fa-plus', 'route' => '#'],
            ['label' => 'Consultation', 'icon' => 'fas fa-search', 'route' => '#']
        ];
    }

    protected function rhSubmenu(): array
    {
        return  [
            /** ======== Ordre de mission (DOM) ========== */
            [
                'label' => 'Ordre de Mission',
                'icon' => 'fas fa-plane',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->domSubmenu()
            ],
            /** ======== Mutation ========== */
            [
                'label' => 'Mutation',
                'icon' => 'fas fa-exchange-alt',
                'route' => null,
                'submenu' => $this->mutationSubmenu()
            ],
            /** ======== Congé ========== */
            [
                'label' => 'Congé',
                'icon' => 'fas fa-umbrella-beach',
                'route' => null,
                'submenu' => $this->congeSubmenu()
            ],
            /** ======== Temporaire ========== */
            [
                'label' => 'Temporaire',
                'icon' => 'fas fa-user-clock',
                'route' => null,
                'submenu' => $this->temporaireSubmenu()
            ]
        ];
    }
    /**<<<--------------  RH  -------------------*/
}
