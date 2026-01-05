<?php

namespace App\Service\Traits;

trait MaterielSubmenuTrait
{
    /** >>>---------- MATERIEL ------------*/
    protected function badmSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'route' => 'hf_materiel_badm_first_form_index', 'icon' => 'fas fa-plus'],
            ['label' => 'Consultation', 'route' => 'hf_materiel_badm_liste_index', 'icon' => 'fas fa-search']
        ];
    }
    protected function casierSubmenu(): array
    {
        return [
            ['label' => 'Nouvelle demande', 'route' => 'hf_materiel_casier_first_form_index'],
            ['label' => 'Consultation', 'route' => 'hf_materiel_casier_liste_index']
        ];
    }

    protected function materielSubmenu(): array
    {
        return [
            // ======== Mouvemnet matériel ========== 
            [
                'label' => 'Mouvemnet matériel',
                'icon' => 'fas fa-tags',
                'route' => null,
                'submenu' => $this->badmSubmenu()
            ],
            // ======== Casier ========== 
            [
                'label' => 'Casier',
                'icon' => 'fas fa-box',
                'route' => null,
                'submenu' => $this->casierSubmenu()
            ],
            // ======== Commandes matériel ========== 
            [
                'label' => 'Commandes matériel',
                'route' => '#',
            ],
            // ======== Suivi administratif des matériels ========== 
            [
                'label' => 'Suivi administratif des matériels',
                'route' => '#',
            ],
        ];
    }

    /** <<<---------- MATERIEL ------------*/
}
