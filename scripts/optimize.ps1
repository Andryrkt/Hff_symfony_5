# Script d'optimisation pour la production (Windows PowerShell)
Write-Host "🚀 Optimisation de l'application Symfony pour la production..." -ForegroundColor Green

# 1. Optimiser l'autoloader Composer
Write-Host "📦 Optimisation de l'autoloader Composer..." -ForegroundColor Yellow
composer install --no-dev --optimize-autoloader --classmap-authoritative

# 2. Vider et réchauffer le cache
Write-Host "🗑️  Nettoyage du cache..." -ForegroundColor Yellow
php bin/console cache:clear --env=prod --no-debug

# 3. Réchauffer le cache
Write-Host "🔥 Réchauffement du cache..." -ForegroundColor Yellow
php bin/console cache:warmup --env=prod

# 4. Optimiser les assets
Write-Host "🎨 Optimisation des assets..." -ForegroundColor Yellow
npm run build

# 5. Optimiser les proxies Doctrine
Write-Host "🗄️  Optimisation des proxies Doctrine..." -ForegroundColor Yellow
php bin/console doctrine:cache:clear-metadata --env=prod
php bin/console doctrine:cache:clear-query --env=prod
php bin/console doctrine:cache:clear-result --env=prod

# 6. Vérifier les permissions (Windows)
Write-Host "🔐 Vérification des permissions..." -ForegroundColor Yellow
if (Test-Path "var") { icacls var /grant Everyone:F /T }
if (Test-Path "public/build") { icacls public/build /grant Everyone:F /T }

Write-Host "✅ Optimisation terminée !" -ForegroundColor Green
Write-Host "📊 Votre application est maintenant optimisée pour la production." -ForegroundColor Green
