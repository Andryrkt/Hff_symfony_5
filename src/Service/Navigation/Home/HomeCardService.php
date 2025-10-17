<?php

namespace App\Service\Navigation\Home;

class HomeCardService
{
    public function getHomeCards(): array
    {
        return [
            // Card Produits
            (new HomeCard(
                'Produits',
                'Gestion des produits et catalogues',
                'fas fa-box',
                'success'
            ))
                ->addLink('Liste des produits', 'product_list')
                ->addLink('Nouveau produit', 'product_new')
                ->addLink('Catégories', 'category_list')
                ->addLink('Inventaire', 'inventory_management')
                ->addLink('Fournisseurs', 'supplier_list'),

            // Card Utilisateurs
            (new HomeCard(
                'Utilisateurs',
                'Gestion des comptes utilisateurs',
                'fas fa-users',
                'info'
            ))
                ->addLink('Liste des utilisateurs', 'user_list')
                ->addLink('Créer un utilisateur', 'user_new')
                ->addLink('Rôles et permissions', 'role_management')
                ->addLink('Activité récente', 'user_activity'),

            // Commandes
            (new HomeCard(
                'Commandes',
                'Suivi et gestion des commandes',
                'fas fa-shopping-cart',
                'warning'
            ))
                ->addLink('Commandes en cours', 'order_list', ['status' => 'pending'])
                ->addLink('Commandes terminées', 'order_list', ['status' => 'completed'])
                ->addLink('Statistiques', 'order_stats')
                ->addLink('Retours', 'order_returns'),

            // Analytics
            (new HomeCard(
                'Analytics',
                'Statistiques et rapports',
                'fas fa-chart-bar',
                'danger'
            ))
                ->addLink('Tableau de bord', 'analytics_dashboard')
                ->addLink('Rapports ventes', 'sales_reports')
                ->addLink('Performance produits', 'product_performance')
                ->addLink('Analytics visiteurs', 'visitor_analytics'),

            // Paramètres
            (new HomeCard(
                'Paramètres',
                'Configuration du système',
                'fas fa-cogs',
                'secondary'
            ))
                ->addLink('Général', 'settings_general')
                ->addLink('Notifications', 'settings_notifications')
                ->addLink('Sécurité', 'settings_security')
                ->addLink('Backup', 'settings_backup')
        ];
    }

    public function getCardByIndex(int $index): ?HomeCard
    {
        $cards = $this->getHomeCards();
        return $cards[$index] ?? null;
    }
}
