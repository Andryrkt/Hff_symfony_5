<?php

namespace App\Service\Home;

class HomeCardService
{
    public function getHomeCards(): array
    {
        return [
            // Card Documentation
            new HomeCard(
                'Documentation',
                'Gestion des documentaions',
                'fas fa-box',
                'success',
                [
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
                    // ['label' => 'Fournisseurs', 'route' => 'supplier_list', 'newTab' => true, 'icon' => 'fas fa-truck'], // Ouvre dans un nouvel onglet
                ]
            ),

            // Card Reporting
            new HomeCard(
                'Reporting',
                'Gestion des comptes utilisateurs',
                'fas fa-users',
                'info',
                [
                    ['label' => 'Reporting Power BI', 'route' => '#'],
                    ['label' => 'Reporting Excel', 'route' => '#']
                ]
            ),

            // Compta
            new HomeCard(
                'Compta',
                'Statistiques et rapports',
                'fas fa-chart-bar',
                'danger',
                [
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
                ]
            ),

            // RH
            new HomeCard(
                'RH',
                'Suivi et gestion des commandes',
                'fas fa-shopping-cart',
                'warning',
                [
                    [
                        'label' => 'Ordre de mission',
                        'icon' => 'fas fa-tags',
                        'children' => [
                            ['label' => 'Nouvelle demande', 'route' => '#', 'icon' => 'fas fa-search'],
                            ['label' => 'Consultation', 'route' => '#', 'icon' => 'fas fa-plus'],
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
                ]
            ),



            // Matériel
            new HomeCard(
                'Matériel',
                'Configuration du système',
                'fas fa-cogs',
                'secondary',
                [
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
                ]
            ),


            /**=========================== Atelier ===============================*/
            new HomeCard(
                'Atelier',
                'Configuration du système',
                'fas fa-cogs',
                'secondary',
                [
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
                ]
            ),

            /**=========================== Magasin ===============================*/
            new HomeCard(
                'Magasin',
                'Configuration du système',
                'fas fa-cogs',
                'secondary',
                [
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
                ]
            ),

            /**=========================== Appro ===============================*/
            new HomeCard(
                'Appro',
                'Configuration du système',
                'fas fa-cogs',
                'secondary',
                [
                    ['label' => 'Nouvelle DA', 'route' => '#'],
                    ['label' => 'Consultation des DA', 'route' => '#'],
                    ['label' => 'Liste des commandes fournisseurs', 'route' => '#'],
                ]
            ),

            /**=========================== IT ===============================*/
            new HomeCard(
                'IT',
                'Configuration du système',
                'fas fa-cogs',
                'secondary',
                [
                    ['label' => 'Nouvelle Demande', 'route' => '#'],
                    ['label' => 'Consultation', 'route' => '#'],
                    ['label' => 'Planning', 'route' => '#'],
                ]
            ),

            /**=========================== POL ===============================*/
            new HomeCard(
                'POL',
                'Configuration du système',
                'fas fa-cogs',
                'secondary',
                [
                    ['label' => 'Nouvelle DLUB', 'route' => '#'],
                    ['label' => 'Consultation des DLUB', 'route' => '#'],
                    ['label' => 'Liste des commandes fournisseur', 'route' => '#'],
                    ['label' => 'Pneumatiques', 'route' => '#'],
                ]
            ),

            /**=========================== Energie ===============================*/
            new HomeCard(
                'Energie',
                'Configuration du système',
                'fas fa-cogs',
                'secondary',
                [
                    ['label' => 'Rapport de production centrale', 'route' => '#'],
                ]
            ),

            /**=========================== HSE ===============================*/
            new HomeCard(
                'HSE',
                'Configuration du système',
                'fas fa-cogs',
                'secondary',
                [
                    ['label' => 'Rapport d\'incident', 'route' => '#'],
                    ['label' => 'Documentation', 'route' => '#']
                ]
            ),
        ];
    }

    public function getCardByIndex(int $index): ?HomeCard
    {
        $cards = $this->getHomeCards();
        return $cards[$index] ?? null;
    }
}
