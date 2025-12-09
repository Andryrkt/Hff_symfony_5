<?php

namespace App\Service\Home;

use App\Repository\Admin\ApplicationGroupe\VignetteRepository;
use Symfony\Component\Security\Core\Security;

class HomeCardService
{
    private $vignetteRepository;
    private $security;

    public function __construct(VignetteRepository $vignetteRepository, Security $security)
    {
        $this->vignetteRepository = $vignetteRepository;
        $this->security = $security;
    }

    public function getHomeCards(): array
    {
        $vignettes = $this->vignetteRepository->findForHomeCards();
        $cards = [];

        foreach ($vignettes as $vignette) {
            if ($this->security->isGranted('APPLICATION_ACCESS', $vignette)) {
                $cardData = $this->getCardData($vignette->getNom());
                $cards[] = new HomeCard(
                    $vignette->getNom(),
                    $vignette->getDescription() ?? '',
                    $cardData['icon'],
                    $cardData['color'],
                    $this->getLinksForVignette($vignette->getNom())
                );
            }
        }

        return $cards;
    }

    private function getCardData(string $vignetteName): array
    {
        $cardData = [
            'Documentation' => ['icon' => 'fas fa-box', 'color' => 'success'],
            'Reporting' => ['icon' => 'fas fa-users', 'color' => 'info'],
            'Compta' => ['icon' => 'fas fa-chart-bar', 'color' => 'danger'],
            'RH' => ['icon' => 'fas fa-shopping-cart', 'color' => 'warning'],
            'Matériel' => ['icon' => 'fas fa-cogs', 'color' => 'secondary'],
            'Atelier' => ['icon' => 'fas fa-cogs', 'color' => 'secondary'],
            'Magasin' => ['icon' => 'fas fa-cogs', 'color' => 'secondary'],
            'Appro' => ['icon' => 'fas fa-cogs', 'color' => 'secondary'],
            'IT' => ['icon' => 'fas fa-cogs', 'color' => 'secondary'],
            'POL' => ['icon' => 'fas fa-cogs', 'color' => 'secondary'],
            'Energie' => ['icon' => 'fas fa-cogs', 'color' => 'secondary'],
            'HSE' => ['icon' => 'fas fa-cogs', 'color' => 'secondary'],
        ];

        return $cardData[$vignetteName] ?? ['icon' => 'fas fa-question-circle', 'color' => 'secondary'];
    }

    private function getLinksForVignette(string $vignetteName): array
    {
        // This is where the hardcoded links are mapped to the vignette name
        $allLinks = [
            'Documentation' => [
                ['label' => 'Annuaire', 'route' => '#', 'icon' => 'fas fa-list-ul'],
                ['label' => 'plan analytique Hff', 'route' => '#', 'icon' => 'fas fa-plus-circle'],
                ['label' => 'Documentaion interne', 'route' => '#', 'icon' => 'fas fa-clipboard-check'],
                [
                    'label' => 'Contrat', // Lien parent pour le sous-menu
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouveau contrat', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Consultation', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
            ],
            'Reporting' => [
                ['label' => 'Reporting Power BI', 'route' => '#'],
                ['label' => 'Reporting Excel', 'route' => '#']
            ],
            'Compta' => [
                ['label' => 'Cours de change', 'route' => '#'],
                [
                    'label' => 'Demande de paiement', // Lien parent pour le sous-menu
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouvelle demande', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Consultation', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
                [
                    'label' => 'Bon de caisse', // Lien parent pour le sous-menu
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouvelle demande', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Consultation', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
            ],
            'RH' => [
                [
                    'label' => 'Ordre de mission',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouvelle demande', 'route' => 'dom_first_form', 'icon' => 'fas fa-search'],
                        ['label' => 'Consultation', 'route' => 'liste_dom_index', 'icon' => 'fas fa-plus'],
                    ]
                ],
                [
                    'label' => 'Mutations',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouvelle demande', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Consultation', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
                [
                    'label' => 'Congé',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouvelle demande', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Consultation', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
                [
                    'label' => 'Temporaire',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouvelle demande', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Consultation', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
            ],
            'Matériel' => [
                [
                    'label' => 'Mouvemnet matériel',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouvelle demande', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Consultation', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
                [
                    'label' => 'Casier',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouvelle demande', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Consultation', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
                ['label' => 'Commandes matériel', 'route' => '#'],
                ['label' => 'Suivi administratif des matériels', 'route' => '#'],
            ],
            'Atelier' => [
                [
                    'label' => 'Demande d\'intervention',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouvelle demande', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Consultation', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
                ['label' => 'Glossaire OR', 'route' => '#'],
                ['label' => 'planning', 'route' => '#'],
                ['label' => 'Planning détaillé', 'route' => '#'],
                ['label' => 'planning interne Atelier', 'route' => '#'],
                ['label' => 'Satisfaction client (Atelier excellence survey)', 'route' => '#'],
            ],
            'Magasin' => [
                [
                    'label' => 'OR',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Liste à traiter', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Liste à livrer', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
                [
                    'label' => 'CIS',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Liste à traiter', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Liste à livrer', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
                [
                    'label' => 'INVENTAIRE',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Liste inventaire', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Inventaire détaillé', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],
                [
                    'label' => 'SORTIE DE PIECES',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Nouvelle demande', 'route' => '#', 'icon' => 'fas fa-search'],
                    ]
                ],
                [
                    'label' => 'DEMATERIALISATION',
                    'icon' => 'fas fa-tags',
                    'children' => [
                        ['label' => 'Devis', 'route' => '#', 'icon' => 'fas fa-search'],
                        ['label' => 'Commandes clients', 'route' => '#', 'icon' => 'fas fa-plus'],
                        ['label' => 'Planning magasin', 'route' => '#', 'icon' => 'fas fa-plus'],
                    ]
                ],

                ['label' => 'Soumission commandes fournisseur', 'route' => '#'],
                ['label' => 'Liste des non placées', 'route' => '#'],
            ],
            'Appro' => [
                ['label' => 'Nouvelle DA', 'route' => '#'],
                ['label' => 'Consultation des DA', 'route' => '#'],
                ['label' => 'Liste des commandes fournisseurs', 'route' => '#'],
            ],
            'IT' => [
                ['label' => 'Nouvelle Demande', 'route' => '#'],
                ['label' => 'Consultation', 'route' => '#'],
                ['label' => 'Planning', 'route' => '#'],
            ],
            'POL' => [
                ['label' => 'Nouvelle DLUB', 'route' => '#'],
                ['label' => 'Consultation des DLUB', 'route' => '#'],
                ['label' => 'Liste des commandes fournisseur', 'route' => '#'],
                ['label' => 'Pneumatiques', 'route' => '#'],
            ],
            'Energie' => [
                ['label' => 'Rapport de production centrale', 'route' => '#'],
            ],
            'HSE' => [
                ['label' => 'Rapport d\'incident', 'route' => '#'],
                ['label' => 'Documentation', 'route' => '#']
            ],
        ];

        return $allLinks[$vignetteName] ?? [];
    }

    public function getCardByIndex(int $index): ?HomeCard
    {
        $cards = $this->getHomeCards();
        return $cards[$index] ?? null;
    }

    public function getCardByName(string $name): ?HomeCard
    {
        $vignette = $this->vignetteRepository->findOneForHomeCard($name);

        if (!$vignette || !$this->security->isGranted('APPLICATION_ACCESS', $vignette)) {
            return null;
        }

        $cardData = $this->getCardData($vignette->getNom());

        return new HomeCard(
            $vignette->getNom(),
            $vignette->getDescription() ?? '',
            $cardData['icon'],
            $cardData['color'],
            $this->getLinksForVignette($vignette->getNom())
        );
    }
}
