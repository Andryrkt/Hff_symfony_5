# 📦 HFF INTRANET

## 📝 Description

Application Symfony 5 permettant de faire une dématérialisation .

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
- extension ODBC activer en php.ini tsy apache (pour voir si activer ou non, executer cette commande "php -m" )
- [Autres prérequis éventuels]

## 🛠️ Installation

```bash
git clone https://github.com/Andryrkt/Hff_symfony_5.git
cd Hff_symfony_5
composer install
yarn install ou npm install
cp .env .env.local
# Modifier .env.local avec vos infos
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```

## 🗂️ Structure du projet

| Dossier     | Description                                   |
| ----------- | --------------------------------------------- |
| src/        | Code métier (contrôleurs, services, entités…) |
| templates/  | Vues Twig                                     |
| public/     | Fichiers publics (build, index.php)           |
| config/     | Fichiers de configuration                     |
| migrations/ | Fichiers de migration                         |
| tests/      | Tests automatisés                             |
| assers/     | Fichiers (css, js, images)                    |

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

## bibliothèque JS installer

- bootstrap
- font awesome

## page crée

- login

## encore à faire

- accéss de l'utilisateur
- log
- exception: 404, 500, ...


### Reglè de permission de l'utilisateur
- un utilisateur peut avoir un ou plusieurs role
- un utilisateur peut avoir un ou plusieurs application
- un utilisateur peut avoir un ou plusieurs permission sur l'agence et service
    - un utilisateur peut avoir plusieurs agence et service
    - un utilisateur peut avoir un seul agence et plusieur service
    - un utilisateur peut avoir un seul agence et les services qui correspond à cette agence
    - un utilisateur peut avoir plusieurs agence et une seul service
    - un utilisateur peut avoit une seul agence et une seul service
- un utilisateur peut avoir un ou plusieurs goupe


### Les groupes
- goupe Atelier
- groupe magasin
- groupe Rh
- groupe Appro
- groupe rentale

### Les permissions
- Creation de nouveau demande (CREATE)
- visualisation de liste des demande (READ)
    - l'utilisateur connecter ne peut voir que selon la règle de permission donnée à lui
    - faire des soumissions OR, Facture, RI, ... selon les conditions de statut et autres de l'application (SOUMISSION)