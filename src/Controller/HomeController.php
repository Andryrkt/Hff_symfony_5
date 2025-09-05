<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        // Simuler des données pour le menu utilisateur
        $infoUserCours = [
            ['App' => 'BDM'],
            ['App' => 'DOM'],
            ['App' => 'DIT'],
            ['App' => 'MAG'],
            ['App' => 'TIK']
        ];

        // Simuler le breadcrumb
        $breadcrumb = [
            [
                'label' => 'Accueil',
                'icon' => 'fas fa-home'
            ]
        ];

        // Données pour les vignettes du menu principal
        $menuItems = [
            [
                'id' => 'documentation',
                'title' => 'Documentation',
                'icon' => 'fas fa-book',
                'items' => [
                    [
                        'title' => 'Guide utilisateur',
                        'icon' => 'fas fa-info-circle',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    [
                        'title' => 'Manuel administrateur',
                        'icon' => 'fas fa-cog',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    [
                        'title' => 'Procédures métier',
                        'icon' => 'fas fa-clipboard-list',
                        'link' => '#',
                        'target' => '_self'
                    ]
                ]
            ],
            [
                'id' => 'rh',
                'title' => 'RH',
                'icon' => 'fas fa-users',
                'items' => [
                    [
                        'title' => 'ORDRE DE MISSION',
                        'icon' => 'fas fa-plus-circle',
                        'subitems' => [
                            [
                                'title' => 'Nouvelle demande',
                                'icon' => 'fas fa-plus-circle',
                                'link' => '#',
                                'target' => '_self'
                            ],
                            [
                                'title' => 'Consultation',
                                'icon' => 'fas fa-plus-circle',
                                'link' => '#',
                                'target' => '_self'
                            ]
                        ]
                    ],
                    [
                        'title' => 'MES DEMANDES',
                        'icon' => 'fas fa-list',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    [
                        'title' => 'Validation',
                        'icon' => 'fas fa-check-circle',
                        'link' => '#',
                        'target' => '_self'
                    ]
                ]
            ],
            [
                'id' => 'dit',
                'title' => 'DIT',
                'icon' => 'fas fa-tools',
                'items' => [
                    [
                        'title' => 'Nouvelle demande',
                        'icon' => 'fas fa-plus-circle',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    [
                        'title' => 'Mes demandes',
                        'icon' => 'fas fa-list',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    [
                        'title' => 'Validation',
                        'icon' => 'fas fa-check-circle',
                        'link' => '#',
                        'target' => '_self'
                    ]
                ]
            ],
            [
                'id' => 'demat',
                'title' => 'Dématérialisation',
                'icon' => 'fas fa-file-upload',
                'items' => [
                    [
                        'title' => 'Upload de documents',
                        'icon' => 'fas fa-cloud-upload-alt',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    [
                        'title' => 'Gestion des fichiers',
                        'icon' => 'fas fa-folder',
                        'link' => '#',
                        'target' => '_self'
                    ]
                ]
            ],
            [
                'id' => 'dap',
                'title' => 'DAP',
                'icon' => 'fas fa-chart-line',
                'items' => [
                    [
                        'title' => 'Tableaux de bord',
                        'icon' => 'fas fa-chart-bar',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    [
                        'title' => 'Rapports',
                        'icon' => 'fas fa-file-alt',
                        'link' => '#',
                        'target' => '_self'
                    ]
                ]
            ],
            [
                'id' => 'magasin',
                'title' => 'Magasin',
                'icon' => 'fas fa-warehouse',
                'items' => [
                    [
                        'title' => 'Inventaire',
                        'icon' => 'fas fa-boxes',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    [
                        'title' => 'Commandes',
                        'icon' => 'fas fa-shopping-cart',
                        'link' => '#',
                        'target' => '_self'
                    ]
                ]
            ],
            [
                'id' => 'support',
                'title' => 'Support Info',
                'icon' => 'fas fa-headset',
                'items' => [
                    [
                        'title' => 'Tickets',
                        'icon' => 'fas fa-ticket-alt',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    [
                        'title' => 'FAQ',
                        'icon' => 'fas fa-question-circle',
                        'link' => '#',
                        'target' => '_self'
                    ]
                ]
            ],
            [
                'id' => 'reporting',
                'title' => 'Reporting',
                'icon' => 'fas fa-chart-pie',
                'items' => [
                    [
                        'title' => 'Rapports généraux',
                        'icon' => 'fas fa-chart-area',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    [
                        'title' => 'Exports',
                        'icon' => 'fas fa-download',
                        'link' => '#',
                        'target' => '_self'
                    ]
                ]
            ]
        ];

        return $this->render('home/index.html.twig', [
            'infoUserCours' => $infoUserCours,
            'breadcrumb' => $breadcrumb,
            'menuItems' => $menuItems
        ]);
    }
}
