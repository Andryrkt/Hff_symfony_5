#!/bin/bash

# Script d'optimisation pour la production
echo "🚀 Optimisation de l'application Symfony pour la production..."

# 1. Optimiser l'autoloader Composer
echo "📦 Optimisation de l'autoloader Composer..."
composer install --no-dev --optimize-autoloader --classmap-authoritative

# 2. Vider et réchauffer le cache
echo "🗑️  Nettoyage du cache..."
php bin/console cache:clear --env=prod --no-debug

# 3. Réchauffer le cache
echo "🔥 Réchauffement du cache..."
php bin/console cache:warmup --env=prod

# 4. Optimiser les assets
echo "🎨 Optimisation des assets..."
npm run build

# 5. Optimiser les proxies Doctrine
echo "🗄️  Optimisation des proxies Doctrine..."
php bin/console doctrine:cache:clear-metadata --env=prod
php bin/console doctrine:cache:clear-query --env=prod
php bin/console doctrine:cache:clear-result --env=prod

# 6. Vérifier les permissions
echo "🔐 Vérification des permissions..."
chmod -R 755 var/
chmod -R 755 public/build/

echo "✅ Optimisation terminée !"
echo "📊 Votre application est maintenant optimisée pour la production."
