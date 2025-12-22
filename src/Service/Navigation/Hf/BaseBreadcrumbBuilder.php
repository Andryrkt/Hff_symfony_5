<?php

namespace App\Service\Navigation\Hf;

use App\Repository\Admin\ApplicationGroupe\VignetteRepository;
use Symfony\Component\Security\Core\Security;

class BaseBreadcrumbBuilder
{
    protected $vignetteRepository;
    protected $security;

    public function __construct(VignetteRepository $vignetteRepository, Security $security)
    {
        $this->vignetteRepository = $vignetteRepository;
        $this->security = $security;
    }

    /**
     * Filtre les sous-menus en fonction des permissions de l'utilisateur
     * Vérifie l'accès à la vignette correspondante
     */
    private array $localVignetteCache = [];

    /**
     * Filtre les sous-menus en fonction des permissions de l'utilisateur
     * Vérifie l'accès à la vignette correspondante
     */
    protected function filterSubmenuByPermissions(string $vignetteName, array $submenu): array
    {
        // Chargement initial du cache local si vide
        if (empty($this->localVignetteCache)) {
            $vignettes = $this->vignetteRepository->findForHomeCards();
            foreach ($vignettes as $v) {
                $this->localVignetteCache[$v->getNom()] = $v;
            }
        }

        // Récupérer la vignette depuis le cache local (plus de requête SQL ici)
        $vignette = $this->localVignetteCache[$vignetteName] ?? null;

        // Si la vignette n'existe pas ou si l'utilisateur n'a pas accès, retourner un tableau vide
        if (!$vignette || !$this->security->isGranted('APPLICATION_ACCESS', $vignette)) {
            return [];
        }

        // Si l'utilisateur a accès, retourner le sous-menu complet
        return $submenu;
    }

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


    protected function comptaSubmenu(): array
    {
        return [
            // ======== Cours de change ========== 
            [
                'label' => 'Cours de change',
                'route' => '#'
            ],
            // ======== Demande de paiement ========== 
            [
                'label' => 'Demande de paiement',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'route' => '#'],
                    ['label' => 'Consultation', 'route' => '#']
                ]
            ],
            // ======== Bon de caisse ========== 
            [
                'label' => 'Bon de caisse',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'route' => '#'],
                    ['label' => 'Consultation', 'route' => '#']
                ]
            ]
        ];
    }

    protected function reportingSubmenu(): array
    {
        return [
            // ======== Reporting Power BI ========== 
            [
                'label' => 'Reporting Power BI',
                'route' => '#'
            ],
            // ======== Reporting Excel ========== 
            [
                'label' => 'Reporting Excel',
                'route' => '#'
            ]
        ];
    }

    protected function documentationSubmenu(): array
    {
        return [
            // ======== Annuaire ==========
            [
                'label' => 'Annuaire',
                'route' => '#'
            ],
            // ======== Plan analytique Hff ========== 
            [
                'label' => 'Plan analytique Hff',
                'route' => '#'
            ],
            // ======== Documentation interne ========== 
            [
                'label' => 'Documentation interne',
                'route' => '#'
            ],
            // ======== Contrat ========== 
            [
                'label' => 'Contrat',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle contrat', 'route' => '#'],
                    ['label' => 'Consultation', 'route' => '#']
                ]
            ]
        ];
    }

    /** >>>---------- MATERIEL ------------*/
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
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'route' => '#'],
                    ['label' => 'Consultation', 'route' => '#']
                ]
            ],
            // ======== Casier ========== 
            [
                'label' => 'Casier',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'route' => 'hf_materiel_casier_first_form_index'],
                    ['label' => 'Consultation', 'route' => 'hf_materiel_casier_liste_index']
                ]
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
    protected function atelierSubmenu(): array
    {
        return [
            // ======== Demande d'intervention ========== 
            [
                'label' => 'Demande d\'intervention',
                'route' => null,
                'submenu' => [
                    ['label' => 'Nouvelle demande', 'route' => '#'],
                    ['label' => 'Consultation', 'route' => '#']
                ]
            ],
            // ======== Glossaire OR ========== 
            [
                'label' => 'Glossaire OR',
                'route' => '#',
            ],
            // ======== Glossaire OR ========== 
            [
                'label' => 'Glossaire OR',
                'route' => '#',
            ],
            // ======== Planning  ========== 
            [
                'label' => 'Planning',
                'route' => '#',
            ],
            // ======== Planning détaillé ========== 
            [
                'label' => 'Planning détaillé',
                'route' => '#',
            ],
            // ======== Planning interne Atelier ========== 
            [
                'label' => 'Planning interne Atelier',
                'route' => '#',
            ],
            // ======== Satisfaction client (Atelier excellence survey) ========== 
            [
                'label' => 'Satisfaction client (Atelier excellence survey)',
                'route' => '#',
            ]
        ];
    }

    protected function magainSubmenu(): array
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

    protected function approSubmenu(): array
    {
        return [
            // ======== Nouvelle DA ========== 
            [
                'label' => 'Nouvelle DA',
                'route' => '#',
            ],
            // ======== Consultation des DA ========== 
            [
                'label' => 'Consultation des DA',
                'route' => '#',
            ],
            // ======== Liste des commandes fournisseurs ========== 
            [
                'label' => 'Liste des commandes fournisseurs',
                'route' => '#',
            ],
        ];
    }

    protected function itSubmenu(): array
    {
        return [
            // ======== Nouvelle demande ========== 
            [
                'label' => 'Nouvelle demande',
                'route' => '#',
            ],
            // ======== Consultation ========== 
            [
                'label' => 'Consultation',
                'route' => '#',
            ],
            // ======== Planning ========== 
            [
                'label' => 'Planning',
                'route' => '#',
            ],
        ];
    }

    protected function polSubmenu(): array
    {
        return [
            // ======== Nouvelle DLUB ========== 
            [
                'label' => 'Nouvelle DLUB',
                'route' => '#',
            ],
            // ======== Consultation DLUB ========== 
            [
                'label' => 'Consultation DLUB',
                'route' => '#',
            ],
            // ======== Liste des commandes fournisseur ========== 
            [
                'label' => 'Liste des commandes fournisseur',
                'route' => '#',
            ],
            // ======== Pneumatiques ========== 
            [
                'label' => 'Pneumatiques',
                'route' => '#',
            ],
        ];
    }

    protected function energieSubmenu(): array
    {
        return [
            // ======== Rapport de production centrale ========== 
            [
                'label' => 'Rapport de production centrale',
                'route' => '#',
            ]
        ];
    }

    protected function hseSubmenu(): array
    {
        return [
            // ======== Rapport d'incident ========== 
            [
                'label' => 'Rapport d\'incident',
                'route' => '#',
            ],
            // ======== Documentation ========== 
            [
                'label' => 'Documentation',
                'route' => '#',
            ]
        ];
    }

    protected function hfSubmenu(): array
    {
        return array_filter([
            /** =============== Documentation ===================== */
            [
                'label' => 'documentation',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Documentation', $this->documentationSubmenu())
            ],
            /** ======== Reporting ========== */
            [
                'label' => 'Reporting',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Reporting', $this->reportingSubmenu())
            ],
            /** ======== Compta ========== */
            [
                'label' => 'Compta',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Compta', $this->comptaSubmenu())
            ],
            /** ======== RH ========== */
            [
                'label' => 'rh',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('RH', $this->rhSubmenu())
            ],
            /** ======== Matériel ========== */
            [
                'label' => 'Matériel',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Matériel', $this->materielSubmenu())
            ],
            /** ======== Atelier ========== */
            [
                'label' => 'Atelier',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Atelier', $this->atelierSubmenu())
            ],
            /** ======== Magasin ========== */
            [
                'label' => 'Magasin',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Magasin', $this->magainSubmenu())
            ],
            /** ======== Appro ========== */
            [
                'label' => 'Appro',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Appro', $this->approSubmenu())
            ],
            /** ======== IT ========== */
            [
                'label' => 'IT',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('IT', $this->itSubmenu())
            ],
            /** ======== POL ========== */
            [
                'label' => 'POL',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('POL', $this->polSubmenu())
            ],
            /** ======== Energie ========== */
            [
                'label' => 'Energie',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Energie', $this->energieSubmenu())
            ],
            /** ======== HSE ========== */
            [
                'label' => 'HSE',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('HSE', $this->hseSubmenu())
            ],
        ], function ($item) {
            // Filtrer les éléments dont le sous-menu est vide (pas d'accès)
            return !empty($item['submenu']);
        });
    }
}
