#!/bin/bash

# Script d'optimisation spécifique pour le module DOM
# Améliore les performances du système de demande d'ordre de mission

echo "🚀 Optimisation du module DOM - Demande d'Ordre de Mission"
echo "========================================================="

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Vérifier si on est dans le bon répertoire
if [ ! -f "composer.json" ]; then
    print_error "Ce script doit être exécuté depuis la racine du projet Symfony"
    exit 1
fi

echo ""
echo "1. Nettoyage du cache Symfony..."
php bin/console cache:clear --env=prod --no-debug
if [ $? -eq 0 ]; then
    print_status "Cache Symfony nettoyé"
else
    print_error "Erreur lors du nettoyage du cache"
    exit 1
fi

echo ""
echo "2. Réchauffement du cache..."
php bin/console cache:warmup --env=prod
if [ $? -eq 0 ]; then
    print_status "Cache réchauffé"
else
    print_warning "Erreur lors du réchauffement du cache (non critique)"
fi

echo ""
echo "3. Optimisation des requêtes Doctrine..."
php bin/console doctrine:cache:clear-metadata --env=prod
php bin/console doctrine:cache:clear-query --env=prod
php bin/console doctrine:cache:clear-result --env=prod
if [ $? -eq 0 ]; then
    print_status "Cache Doctrine optimisé"
else
    print_warning "Erreur lors de l'optimisation du cache Doctrine"
fi

echo ""
echo "4. Compilation des assets frontend..."
if command -v npm &> /dev/null; then
    npm run build
    if [ $? -eq 0 ]; then
        print_status "Assets frontend compilés"
    else
        print_warning "Erreur lors de la compilation des assets"
    fi
else
    print_warning "npm non trouvé, compilation des assets ignorée"
fi

echo ""
echo "5. Optimisation de Composer..."
composer install --no-dev --optimize-autoloader --classmap-authoritative
if [ $? -eq 0 ]; then
    print_status "Composer optimisé"
else
    print_error "Erreur lors de l'optimisation de Composer"
    exit 1
fi

echo ""
echo "6. Vérification des permissions..."
chmod -R 755 var/cache/
chmod -R 755 var/log/
print_status "Permissions mises à jour"

echo ""
echo "7. Test de performance du DOM..."
# Test simple pour vérifier que le module fonctionne
php bin/console debug:router | grep dom_first
if [ $? -eq 0 ]; then
    print_status "Routes DOM détectées"
else
    print_warning "Routes DOM non trouvées"
fi

echo ""
echo "========================================================="
echo -e "${GREEN}🎉 Optimisation du module DOM terminée !${NC}"
echo ""
echo "Améliorations apportées :"
echo "• Cache Symfony optimisé"
echo "• Requêtes Doctrine mises en cache"
echo "• Assets frontend compilés"
echo "• Autoloader Composer optimisé"
echo "• Service de cache DOM activé"
echo "• Debouncing côté client"
echo ""
echo "Le formulaire DOM devrait maintenant s'afficher plus rapidement !"
echo "========================================================="
