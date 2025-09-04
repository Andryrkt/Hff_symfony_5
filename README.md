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
# 1. Cloner le projet
git clone https://github.com/Andryrkt/Hff_symfony_5.git
cd Hff_symfony_5

# 2. Installer les dépendances
composer install
npm install

# 3. Configurer l'environnement
cp .env.example .env
# Modifier .env avec vos vraies valeurs (voir docs/ENVIRONMENT_SETUP.md)

# 4. Initialiser la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. Compiler les assets
npm run build

# 6. Démarrer le serveur
symfony server:start
```

## 🧹 Nettoyage Git

Pour nettoyer les fichiers qui ne devraient pas être versionnés :

```bash
# Exécuter le script de nettoyage
./scripts/clean-git.sh

# Ou manuellement
git rm --cached .env
git rm -r --cached var/ vendor/ node_modules/
```

## 🚀 Optimisation des performances

```bash
# Script d'optimisation complet
./scripts/optimize-gitbash.sh

# Ou commandes manuelles
composer install --no-dev --optimize-autoloader --classmap-authoritative
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod
npm run build
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
| assets/     | Fichiers frontend (css, js, images)           |
| docs/       | Documentation du projet                       |
| scripts/    | Scripts utilitaires                           |

> 📋 **Voir la structure détaillée** : [docs/PROJECT_STRUCTURE.md](docs/PROJECT_STRUCTURE.md)

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

## pour tester la page d'erreur 
- throw new NotFoundHttpException('Page non trouvée');
- throw new AccessDeniedHttpException('Accès interdit');
- throw new \Exception('Erreur interne du serveur');

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
- un utilisateur peut avoir un ou plusieurs groupe


### Les groupes
- goupe Atelier
- groupe magasin
- groupe Rh
- groupe Appro
- groupe Rentale
- groupe Energie
- groupe compta

### Les permissions
- Creation de nouveau demande (CREATE)
- visualisation de liste des demande (READ)
    - l'utilisateur connecter ne peut voir que selon la règle de permission donnée à lui
    - faire des soumissions OR, Facture, RI, ... selon les conditions de statut et autres de l'application (SOUMISSION)

### Rôle
- ROLE_USER
- ROLE_ADMIN
- ROLE_SUPPER_ADMIN
- ROLE_CHEF_SERVICE
- ROLE_DOM


   1. Relations Clés : L'entité UserAccess est bien le pivot central. Elle relie quatre concepts :
       * private $users; (Un utilisateur)
       * private $agence; (Une agence)
       * private $service; (Un service)
       * private $application; (Une application, ce qui permet de gérer les droits pour différents modules du projet, par exemple "Ordres de Mission", "Facturation", etc.)

   2. Le champ `accessType` : C'est un détail très important. Le commentaire // ALL, AGENCE, SERVICE, AGENCE_SERVICE suggère une gestion des droits très flexible :
       * ALL : L'utilisateur a accès à tout, sans restriction.
       * AGENCE : L'utilisateur a accès à tous les services d'une agence donnée.
       * SERVICE : L'utilisateur a accès à un service spécifique, mais dans toutes les agences.
       * AGENCE_SERVICE : C'est le cas le plus restrictif. L'utilisateur n'a accès qu'à un service précis dans une agence précise.

    ┌────────────────┬─────────────────────────────────────────┬───────────────────────────────────────────────────────────────────────────────────────────────────┐
  │ AccessType     │ Paire (Agence, Service)                 │ Signification dans le code                                                                        │
  ├────────────────┼─────────────────────────────────────────┼───────────────────────────────────────────────────────────────────────────────────────────────────┤
  │ `ALL`            │ (null, null) ou "Non applicable"        │ Aucun filtre. La requête ne contient pas de WHERE sur l'agence ou le service.                     │
  │ AGENCE         │ (Agence Spécifique, *)                  │ Filtre sur l'agence uniquement : WHERE p.agence = :id_agence                                      │
  │ SERVICE        │ (*, Service Spécifique)                 │ Filtre sur le service uniquement : WHERE p.service = :id_service                                  │
  │ AGENCE_SERVICE │ (Agence Spécifique, Service Spécifique) │ Filtre le plus strict sur la paire exacte : WHERE p.agence = :id_agence AND p.service = :id_service │
  └────────────────┴─────────────────────────────────────────┴───────────────────────────────────────────────────────────────────────────────────────────────────┘


  ENCORE à fair
  - ajout du type scipte
  - ajut de rabbitMQ
  - mettre l'aégence et service debitteur dans une autre ficher