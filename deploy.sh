#!/bin/bash

# Script de déploiement pour HFF Intranet
# Usage: ./deploy.sh [dev|prod]

set -e

ENVIRONMENT=${1:-prod}
PROJECT_DIR=$(pwd)

echo "🚀 Déploiement HFF Intranet - Environnement: $ENVIRONMENT"
echo "=================================================="

# Vérifications préalables
echo "📋 Vérifications préalables..."

if ! command -v php &> /dev/null; then
    echo "❌ PHP n'est pas installé"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo "❌ Composer n'est pas installé"
    exit 1
fi

if ! command -v yarn &> /dev/null; then
    echo "❌ Yarn n'est pas installé"
    exit 1
fi

echo "✅ Vérifications terminées"

# Sauvegarde de la base de données (si en production)
if [ "$ENVIRONMENT" = "prod" ]; then
    echo "💾 Sauvegarde de la base de données..."
    php bin/console doctrine:database:backup --env=prod || echo "⚠️  Sauvegarde échouée, continuation..."
fi

# Mise à jour des dépendances
echo "📦 Mise à jour des dépendances PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "📦 Mise à jour des dépendances Node.js..."
yarn install --frozen-lockfile

# Compilation des assets
echo "🔨 Compilation des assets..."
yarn build

# Vider le cache
echo "🧹 Vidage du cache..."
php bin/console cache:clear --env=$ENVIRONMENT

# Migrations de base de données
echo "🗄️  Application des migrations..."
php bin/console doctrine:migrations:migrate --env=$ENVIRONMENT --no-interaction

# Vérification de la sécurité
echo "🔒 Vérification de la sécurité..."
php bin/console security:check || echo "⚠️  Vérification de sécurité échouée"

# Optimisations pour la production
if [ "$ENVIRONMENT" = "prod" ]; then
    echo "⚡ Optimisations pour la production..."
    
    # Optimisation du cache
    php bin/console cache:warmup --env=prod
    
    # Vérification des permissions
    chmod -R 755 var/cache/
    chmod -R 755 var/log/
    
    echo "✅ Optimisations terminées"
fi

# Tests (optionnel en production)
if [ "$ENVIRONMENT" = "dev" ]; then
    echo "🧪 Exécution des tests..."
    php bin/phpunit --stop-on-failure || echo "⚠️  Tests échoués, continuation..."
fi

echo "🎉 Déploiement terminé avec succès!"
echo "=================================================="
echo "📊 Informations:"
echo "   - Environnement: $ENVIRONMENT"
echo "   - Cache vidé"
echo "   - Assets compilés"
echo "   - Migrations appliquées"
echo ""
echo "🌐 Pour démarrer le serveur:"
echo "   symfony server:start --env=$ENVIRONMENT"
echo ""
echo "📝 Logs disponibles dans: var/log/" 