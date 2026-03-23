## —— 🐝 Makefile pour Symfony 🐝 ———————————————————————————————————
help: ## Affiche cette aide
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Composer 🧙‍♂️ ——————————————————————————————————————————————————
install: composer.lock ## Installe les dépendances Composer
	composer install

update: ## Met à jour les dépendances Composer
	composer update

## —— Symfony 🎵 —————————————————————————————————————————————————————
console = php bin/console

cc: ## Vide le cache
	$(console) cache:clear

cache-warmup: ## Précharge le cache
	$(console) cache:warmup

## —— Doctrine 🪄 —————————————————————————————————————————————————————
migrate: ## Exécute les migrations
	$(console) doctrine:migrations:migrate --no-interaction

diff: ## Génère une migration à partir des changements d'entités
	$(console) doctrine:migrations:diff

fixtures: ## Charge les fixtures (si vous utilisez Doctrine Fixtures)
	$(console) doctrine:fixtures:load --no-interaction

## —— Migration command 🪄 ——————————————————————————————————————————————
migration-all-dry-run:
	$(console) app:migrate:personnel-data --dry-run --limit=10
	$(console) app:migrate:user-data --dry-run --limit=10
	$(console) app:migrate:casier-data --dry-run --limit=10

#$(console) app:migrate:dom-data --dry-run --limit=10

migration-all:
	$(console) app:migrate:personnel-data --batch-size=50
	$(console) app:migrate:user-data --batch-size=50
	$(console) app:migrate:casier-data --batch-size=50
	
#$(console) app:migrate:dom-data --batch-size=50

delete-all:
	$(console) app:delete-personnel-data
	$(console) app:delete-user-data
	$(console) app:delete-dom-data
	$(console) app:delete-casier-data

## —— Qualité de code 📊 ———————————————————————————————————————————————
test: ## Lance les tests PHPUnit
	php bin/phpunit

cs-fix: ## Corrige le code avec PHP CS Fixer (si installé)
	php vendor/bin/php-cs-fixer fix

stan: ## Analyse statique avec PHPStan (si installé)
	php vendor/bin/phpstan analyse

## —— Autres ————————————————————————————————————————————————————————
.DEFAULT_GOAL := help
.PHONY: help install update cc cache-warmup migrate diff fixtures test cs-fix stan