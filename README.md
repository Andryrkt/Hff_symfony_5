# 📦 HFF INTRANET

## 📝 Description

Application Symfony 5 pour l'intranet HFF, gérant la dématérialisation des processus administratifs (RH, Compta, Matériel...).

> 📚 **Documentation Complète** : [Consulter le wiki technique](docs/README.md)

## 🚀 Technologies

-   **Backend**: PHP 7.4+, Symfony 5.4, Doctrine ORM (API Platform)
-   **Frontend**: Twig, Webpack Encore, Bootstrap 5
-   **Base de données**: SQL Server 2019 / Informix
-   **Authentification**: LDAP

## 🛠️ Installation Rapide

Pour les détails complets, voir la [Documentation de Déploiement](docs/deployment.md).

```bash
# 1. Installer les dépendances
composer install
npm install

# 2. Configurer l'environnement
cp .env.example .env.local
# (Configurer DB et Mailer dans .env.local)

# 3. Base de données
php bin/console doctrine:migrations:migrate

# 4. Lancer le serveur
symfony server:start
npm run watch
```

## 🗂️ Documentation

La documentation détaillée est disponible dans le dossier `docs/` :

-   [🏛️ Architecture](docs/architecture.md) : Structure du code, modules "Vignettes".
-   [🗄️ Base de Données](docs/database.md) : Modèle de données, gestion des droits `UserAccess`.
-   [🔐 Sécurité](docs/security.md) : LDAP, Voters, Permissions.
-   [🚀 Déploiement](docs/deployment.md) : Scripts d'optimisation, mise en production.

## 🧹 Maintenance

Commandes utiles pour le nettoyage et l'optimisation :

```bash
# Nettoyage fichiers temporaires
./scripts/clean-git.sh

# Optimisation prod
./scripts/optimize-gitbash.sh
```

## 🧪 Tests

```bash
php bin/phpunit
```