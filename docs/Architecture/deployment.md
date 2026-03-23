# 🚀 Déploiement et Environnement

## Prérequis Serveur

-   **OS** : Windows Server (IIS).
-   **PHP** : 7.4 minimum.
-   **Base de données** : SQL Server 2019 et Informix.
-   **Extensions PHP** : `intl`, `mbstring`, `pdo`,`sqlsrv`,`pdo_sqlsrv`,`odbc`,`pdo_odbc`, `ldap`, `zip`.
-   **Outils** : Composer, NodeJS, Git.

## Installation en Production

1.  **Cloner le code** :
    ```bash
    git clone ...
    ```

2.  **Configuration (.env)** :
    Copier `.env` vers `.env.local` et configurer les accès production (BDD, Sockets, Mailer).
    > **Important** : Ne jamais commiter `.env.local`.

3.  **Installation des dépendances** :
    ```bash
    composer install --no-dev --optimize-autoloader
    npm install
    ```

4.  **Build des Assets** :
    ```bash
    npm run build
    ```

## Commandes de Déploiement / Mise à jour

À chaque mise à jour du code, exécuter :

```bash
# 1. Mise à jour du code
git pull

# 2. Mise à jour dépendances (si changées)
composer install --no-dev
npm ci

# 3. Migrations BDD
php bin/console doctrine:migrations:migrate --no-interaction

# 4. Vider le cache (CRITIQUE)
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 5. Rebuild assets (si changés)
npm run build
```

## Scripts d'Optimisation

Le projet contient des scripts pour automatiser le nettoyage et l'optimisation :
-   `scripts/optimize-gitbash.sh` : Lance les commandes de cache et d'autoloader.
-   `scripts/clean-git.sh` : Nettoie les fichiers temporaires.
