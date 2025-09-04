# Configuration Git Bash pour le projet Symfony
# Ce fichier sera chargé automatiquement par Git Bash

# Alias pour les commandes Symfony
alias sf='php bin/console'
alias sf-cache-clear='php bin/console cache:clear'
alias sf-cache-warmup='php bin/console cache:warmup'
alias sf-doctrine-migrate='php bin/console doctrine:migrations:migrate --no-interaction'

# Alias pour les commandes de développement
alias sf-dev='php bin/console --env=dev'
alias sf-prod='php bin/console --env=prod'

# Alias pour les tests
alias sf-test='php bin/phpunit'

# Alias pour les assets
alias npm-dev='npm run dev'
alias npm-build='npm run build'
alias npm-watch='npm run watch'

# Alias pour Composer
alias composer-install='composer install'
alias composer-update='composer update'
alias composer-optimize='composer install --no-dev --optimize-autoloader --classmap-authoritative'

# Alias pour Git
alias gs='git status'
alias ga='git add'
alias gc='git commit'
alias gp='git push'
alias gl='git log --oneline'

# Alias pour les permissions (Windows)
alias fix-permissions='chmod -R 755 var/ public/build/'

# Fonction d'optimisation complète
optimize-symfony() {
    echo "🚀 Optimisation complète de Symfony..."
    composer-optimize
    sf-cache-clear --env=prod --no-debug
    sf-cache-warmup --env=prod
    npm-build
    sf doctrine:cache:clear-metadata --env=prod
    sf doctrine:cache:clear-query --env=prod
    sf doctrine:cache:clear-result --env=prod
    fix-permissions
    echo "✅ Optimisation terminée !"
}

# Fonction de nettoyage
clean-symfony() {
    echo "🧹 Nettoyage de Symfony..."
    sf-cache-clear
    rm -rf var/cache/*
    rm -rf var/log/*
    echo "✅ Nettoyage terminé !"
}

# Affichage du répertoire de travail
export PS1='\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '

# Variables d'environnement utiles
export SYMFONY_ENV=dev

echo "🐚 Git Bash configuré pour Symfony !"
echo "💡 Commandes disponibles : sf, sf-cache-clear, optimize-symfony, clean-symfony"
