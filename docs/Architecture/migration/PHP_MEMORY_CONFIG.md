# Configuration PHP pour la migration DOM

# Augmenter la limite de mémoire pour la migration
# À ajouter dans php.ini ou via la ligne de commande

# Option 1: Modifier php.ini
# memory_limit = 512M

# Option 2: Via la ligne de commande
# php -d memory_limit=512M bin/console app:migrate:dom-data

# Option 3: Variable d'environnement (Windows)
# set PHP_INI_SCAN_DIR=
# php -d memory_limit=512M bin/console app:migrate:dom-data

# Recommandations pour la migration :
# - Utiliser --batch-size=20 pour les gros volumes
# - Utiliser --limit pour migrer par tranches
# - Exemple : php -d memory_limit=512M bin/console app:migrate:dom-data --batch-size=20 --limit=100
