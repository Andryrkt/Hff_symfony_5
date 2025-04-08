# 📦 Nom du projet Symfony 5

## 📝 Description
Application Symfony 5 permettant de dématérialiser .

## 🚀 Technologies
- PHP >= 7.4
- Symfony 5.4
- Doctrine ORM
- Twig
- CSS
- JS
- [Autres outils ou bibliothèques]
- Base de données : SQL Server 2019, Informix


## ⚙️ Prérequis
- PHP installé
- Composer
- Symfony CLI
- yarn ou npm (Node js)
- Serveur web (Apache, Nginx)
- [Autres prérequis éventuels]


## 🛠️ Installation

```bash
git clone https://github.com/ton-org/ton-projet.git
cd ton-projet
composer install
yarn install ou npm install
cp .env .env.local
# Modifier .env.local avec vos infos
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```
## 🗂️ Structure du projet
|Dossier    |	Description                                 |
|-----------|-----------------------------------------------|
|src/	    |Code métier (contrôleurs, services, entités…)  |
|templates/	|Vues Twig                                      |
|public/	|Fichiers publics (build, index.php)            |
|config/	|Fichiers de configuration                      |
|migrations/|Fichiers de migration                          |
|tests/	    |Tests automatisés                              |
|assers/    | Fichiers (css, js, images)                    |


## 🔐 Authentification et sécurité

- Authentification via LDAP

- Gestion des rôles via security.yaml

- Accès restreint à certaines routes


## 🧪 Tests

php bin/phpunit



## 🚀 Déploiement

- Configuration .env.local pour la prod

- Cache, logs, et permissions

- Commandes utiles :
```Bash
    php bin/console cache:clear
    php bin/console doctrine:migrations:migrate
```

## 🧰 Commandes personnalisées

    php bin/console app:ma-commande