<?php

namespace App\Service\Traits;

trait RhSubmenuTrait
{
    /**>>>--------------  RH  -------------------*/
    protected function domSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'route' => 'dom_first_form'],
            ['label' => 'Consultation', 'route' => 'dom_liste_index']
        ];
    }

    protected function mutationSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'route' => '#'],
            ['label' => 'Consultation', 'route' => '#']
        ];
    }

    protected function congeSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'route' => '#'],
            ['label' => 'Consultation', 'route' => '#']
        ];
    }

    protected function temporaireSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'route' => '#'],
            ['label' => 'Consultation', 'route' => '#']
        ];
    }

    protected function rhSubmenu(): array
    {
        return  [
            /** ======== Ordre de mission (DOM) ========== */
            [
                'label' => 'Ordre de Mission',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->domSubmenu()
            ],
            /** ======== Mutation ========== */
            [
                'label' => 'Mutation',
                'route' => null,
                'submenu' => $this->mutationSubmenu()
            ],
            /** ======== Congé ========== */
            [
                'label' => 'Congé',
                'route' => null,
                'submenu' => $this->congeSubmenu()
            ],
            /** ======== Temporaire ========== */
            [
                'label' => 'Temporaire',
                'route' => null,
                'submenu' => $this->temporaireSubmenu()
            ]
        ];
    }
    /**<<<--------------  RH  -------------------*/
}
