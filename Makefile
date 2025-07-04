# Makefile pour HFF Symfony 5
# Usage: make [commande]

.PHONY: help install dev build test cache-clear migrate fixtures deploy-prod deploy-dev

# Variables
SYMFONY_CONSOLE = php bin/console
COMPOSER = composer
YARN = yarn
PHPUNIT = php bin/phpunit

# Couleurs pour les messages
GREEN = \033[0;32m
YELLOW = \033[1;33m
RED = \033[0;31m
NC = \033[0m # No Color

# Commande par défaut
help: ## Affiche cette aide
	@echo "$(GREEN)Commandes disponibles:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(YELLOW)%-15s$(NC) %s\n", $$1, $$2}'

# Installation et configuration
install: ## Installe toutes les dépendances (PHP + Node.js)
	@echo "$(GREEN)📦 Installation des dépendances PHP...$(NC)"
	$(COMPOSER) install
	@echo "$(GREEN)📦 Installation des dépendances Node.js...$(NC)"
	$(YARN) install
	@echo "$(GREEN)✅ Installation terminée$(NC)"

install-prod: ## Installe les dépendances pour la production
	@echo "$(GREEN)📦 Installation des dépendances PHP (production)...$(NC)"
	$(COMPOSER) install --no-dev --optimize-autoloader
	@echo "$(GREEN)📦 Installation des dépendances Node.js...$(NC)"
	$(YARN) install --frozen-lockfile
	@echo "$(GREEN)🔨 Compilation des assets pour la production...$(NC)"
	$(YARN) build
	@echo "$(GREEN)✅ Installation production terminée$(NC)"

# Développement
dev: ## Lance le serveur de développement
	@echo "$(GREEN)🚀 Démarrage du serveur de développement...$(NC)"
	symfony server:start --daemon
	@echo "$(GREEN)📡 Serveur démarré sur http://localhost:8000$(NC)"

dev-stop: ## Arrête le serveur de développement
	@echo "$(YELLOW)🛑 Arrêt du serveur de développement...$(NC)"
	symfony server:stop
	@echo "$(GREEN)✅ Serveur arrêté$(NC)"

dev-watch: ## Lance le serveur avec rechargement automatique
	@echo "$(GREEN)👀 Démarrage en mode watch...$(NC)"
	symfony server:start --daemon
	$(YARN) watch

# Assets et compilation
build: ## Compile les assets pour la production
	@echo "$(GREEN)🔨 Compilation des assets...$(NC)"
	$(YARN) build
	@echo "$(GREEN)✅ Assets compilés$(NC)"

build-dev: ## Compile les assets pour le développement
	@echo "$(GREEN)🔨 Compilation des assets (dev)...$(NC)"
	$(YARN) dev
	@echo "$(GREEN)✅ Assets compilés$(NC)"

watch: ## Surveille les changements d'assets
	@echo "$(GREEN)👀 Surveillance des assets...$(NC)"
	$(YARN) watch

# Base de données
migrate: ## Applique les migrations de base de données
	@echo "$(GREEN)🗄️ Application des migrations...$(NC)"
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction
	@echo "$(GREEN)✅ Migrations appliquées$(NC)"

migrate-status: ## Affiche le statut des migrations
	@echo "$(GREEN)📊 Statut des migrations:$(NC)"
	$(SYMFONY_CONSOLE) doctrine:migrations:status

migrate-diff: ## Génère une nouvelle migration
	@echo "$(GREEN)📝 Génération d'une nouvelle migration...$(NC)"
	$(SYMFONY_CONSOLE) doctrine:migrations:diff

fixtures: ## Charge les fixtures de données
	@echo "$(GREEN)🎭 Chargement des fixtures...$(NC)"
	$(SYMFONY_CONSOLE) doctrine:fixtures:load --no-interaction
	@echo "$(GREEN)✅ Fixtures chargées$(NC)"

fixtures-append: ## Ajoute les fixtures sans vider la base
	@echo "$(GREEN)🎭 Ajout des fixtures...$(NC)"
	$(SYMFONY_CONSOLE) doctrine:fixtures:load --no-interaction --append
	@echo "$(GREEN)✅ Fixtures ajoutées$(NC)"

# Cache et optimisation
cache-clear: ## Vide le cache
	@echo "$(GREEN)🧹 Vidage du cache...$(NC)"
	$(SYMFONY_CONSOLE) cache:clear
	@echo "$(GREEN)✅ Cache vidé$(NC)"

cache-warmup: ## Réchauffe le cache
	@echo "$(GREEN)🔥 Réchauffage du cache...$(NC)"
	$(SYMFONY_CONSOLE) cache:warmup
	@echo "$(GREEN)✅ Cache réchauffé$(NC)"

# Tests
test: ## Lance tous les tests
	@echo "$(GREEN)🧪 Exécution des tests...$(NC)"
	$(PHPUNIT)
	@echo "$(GREEN)✅ Tests terminés$(NC)"

test-coverage: ## Lance les tests avec couverture de code
	@echo "$(GREEN)🧪 Exécution des tests avec couverture...$(NC)"
	$(PHPUNIT) --coverage-html var/coverage
	@echo "$(GREEN)✅ Couverture générée dans var/coverage$(NC)"

test-unit: ## Lance uniquement les tests unitaires
	@echo "$(GREEN)🧪 Exécution des tests unitaires...$(NC)"
	$(PHPUNIT) --testsuite=unit
	@echo "$(GREEN)✅ Tests unitaires terminés$(NC)"

# Sécurité et validation
security-check: ## Vérifie la sécurité
	@echo "$(GREEN)🔒 Vérification de la sécurité...$(NC)"
	$(SYMFONY_CONSOLE) security:check || echo "$(YELLOW)⚠️ Vérification de sécurité échouée$(NC)"

validate: ## Valide la configuration Symfony
	@echo "$(GREEN)✅ Validation de la configuration...$(NC)"
	$(SYMFONY_CONSOLE) debug:config
	@echo "$(GREEN)✅ Configuration validée$(NC)"

# Déploiement
deploy-dev: ## Déploiement en développement
	@echo "$(GREEN)🚀 Déploiement en développement...$(NC)"
	./deploy.sh dev
	@echo "$(GREEN)✅ Déploiement dev terminé$(NC)"

deploy-prod: ## Déploiement en production
	@echo "$(GREEN)🚀 Déploiement en production...$(NC)"
	./deploy.sh prod
	@echo "$(GREEN)✅ Déploiement production terminé$(NC)"

# Maintenance
clean: ## Nettoie les fichiers temporaires
	@echo "$(GREEN)🧹 Nettoyage...$(NC)"
	rm -rf var/cache/*
	rm -rf var/log/*
	rm -rf node_modules/.cache
	@echo "$(GREEN)✅ Nettoyage terminé$(NC)"

reset: ## Remet à zéro l'environnement de développement
	@echo "$(RED)⚠️ Remise à zéro de l'environnement...$(NC)"
	$(SYMFONY_CONSOLE) cache:clear
	$(SYMFONY_CONSOLE) doctrine:database:drop --force --if-exists
	$(SYMFONY_CONSOLE) doctrine:database:create
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction
	$(SYMFONY_CONSOLE) doctrine:fixtures:load --no-interaction
	@echo "$(GREEN)✅ Environnement remis à zéro$(NC)"

# Utilitaires
logs: ## Affiche les logs en temps réel
	@echo "$(GREEN)📝 Affichage des logs...$(NC)"
	tail -f var/log/dev.log

routes: ## Liste toutes les routes
	@echo "$(GREEN)🛣️ Liste des routes:$(NC)"
	$(SYMFONY_CONSOLE) debug:router

services: ## Liste tous les services
	@echo "$(GREEN)🔧 Liste des services:$(NC)"
	$(SYMFONY_CONSOLE) debug:container --tag=service

# Commandes rapides
quick-start: install dev ## Installation rapide et démarrage
	@echo "$(GREEN)🎉 Projet prêt !$(NC)"

quick-reset: cache-clear migrate fixtures ## Reset rapide (cache + migrations + fixtures)
	@echo "$(GREEN)✅ Reset rapide terminé$(NC)" 