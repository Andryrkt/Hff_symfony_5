<?php

namespace App\Service\Navigation\Home;

class HomeCardService
{
    public function getHomeCards(): array
    {
        return [
            // Card Produits
            new HomeCard(
                'Produits',
                'Gestion des produits et catalogues',
                'fas fa-box',
                'success',
                [
                    ['label' => 'Liste des produits', 'route' => 'product_list', 'icon' => 'fas fa-list-ul'],
                    ['label' => 'Nouveau produit', 'route' => 'product_new', 'icon' => 'fas fa-plus-circle'],
                    [
                        'label' => 'Catégories', // Lien parent pour le sous-menu
                        'icon' => 'fas fa-tags',
                        'children' => [
                            ['label' => 'Voir les catégories', 'route' => 'category_list', 'icon' => 'fas fa-search'],
                            ['label' => 'Ajouter une catégorie', 'route' => 'category_add', 'icon' => 'fas fa-plus'],
                        ]
                    ],
                    ['label' => 'Inventaire', 'route' => 'inventory_management', 'icon' => 'fas fa-clipboard-check'],
                    ['label' => 'Fournisseurs', 'route' => 'supplier_list', 'newTab' => true, 'icon' => 'fas fa-truck'], // Ouvre dans un nouvel onglet
                ]
            ),

            // Card Utilisateurs
            new HomeCard(
                'Utilisateurs',
                'Gestion des comptes utilisateurs',
                'fas fa-users',
                'info',
                [
                    ['label' => 'Liste des utilisateurs', 'route' => 'user_list'],
                    ['label' => 'Créer un utilisateur', 'route' => 'user_new'],
                    ['label' => 'Rôles et permissions', 'route' => 'role_management'],
                    ['label' => 'Activité récente', 'route' => 'user_activity'],
                ]
            ),

            // Commandes
            new HomeCard(
                'Commandes',
                'Suivi et gestion des commandes',
                'fas fa-shopping-cart',
                'warning',
                [
                    ['label' => 'Commandes en cours', 'route' => 'order_list', 'params' => ['status' => 'pending']],
                    ['label' => 'Commandes terminées', 'route' => 'order_list', 'params' => ['status' => 'completed']],
                    ['label' => 'Statistiques', 'route' => 'order_stats'],
                    ['label' => 'Retours', 'route' => 'order_returns'],
                ]
            ),

            // Analytics
            new HomeCard(
                'Analytics',
                'Statistiques et rapports',
                'fas fa-chart-bar',
                'danger',
                [
                    ['label' => 'Tableau de bord', 'route' => 'analytics_dashboard'],
                    ['label' => 'Rapports ventes', 'route' => 'sales_reports'],
                    ['label' => 'Performance produits', 'route' => 'product_performance'],
                    ['label' => 'Analytics visiteurs', 'route' => 'visitor_analytics'],
                ]
            ),

            // Paramètres
            new HomeCard(
                'Paramètres',
                'Configuration du système',
                'fas fa-cogs',
                'secondary',
                [
                    ['label' => 'Général', 'route' => 'settings_general'],
                    ['label' => 'Notifications', 'route' => 'settings_notifications'],
                    ['label' => 'Sécurité', 'route' => 'settings_security'],
                    ['label' => 'Backup', 'route' => 'settings_backup'],
                ]
            )
        ];
    }

    public function getCardByIndex(int $index): ?HomeCard
    {
        $cards = $this->getHomeCards();
        return $cards[$index] ?? null;
    }
}