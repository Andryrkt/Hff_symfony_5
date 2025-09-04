# 📁 Structure du Projet HFF Symfony 5

## 🗂️ Organisation des dossiers

```
hff_symfony_5/
├── 📁 assets/                    # Assets frontend (CSS, JS, images)
│   ├── app.ts                    # Point d'entrée TypeScript principal
│   ├── bootstrap.ts              # Bootstrap Stimulus
│   ├── controllers/              # Contrôleurs Stimulus
│   ├── images/                   # Images statiques
│   ├── js/                       # JavaScript/TypeScript
│   └── styles/                   # Styles SCSS/CSS
│
├── 📁 bin/                       # Exécutables Symfony
│   ├── console                   # Console Symfony
│   └── phpunit                   # PHPUnit
│
├── 📁 config/                    # Configuration Symfony
│   ├── bundles.php               # Configuration des bundles
│   ├── packages/                 # Configuration des packages
│   ├── routes/                   # Configuration des routes
│   ├── routes.yaml               # Routes principales
│   └── services.yaml             # Configuration des services
│
├── 📁 docs/                      # Documentation du projet
│   ├── ENVIRONMENT_SETUP.md      # Configuration environnement
│   ├── GUIDE_UTILISATEUR.md      # Guide utilisateur
│   ├── PERFORMANCE_OPTIMIZATIONS.md # Optimisations performances
│   ├── PROJECT_STRUCTURE.md      # Ce fichier
│   ├── README.md                 # Documentation technique
│   ├── assets/                   # Images de documentation
│   ├── fonctionnelle/            # Documentation fonctionnelle
│   └── technique/                # Documentation technique
│
├── 📁 migrations/                # Migrations Doctrine
│   └── Version*.php              # Fichiers de migration
│
├── 📁 public/                    # Point d'entrée web
│   ├── index.php                 # Point d'entrée principal
│   ├── build/                    # Assets compilés
│   └── bundles/                  # Bundles publics
│
├── 📁 scripts/                   # Scripts utilitaires
│   ├── clean-git.sh              # Nettoyage Git
│   ├── deploy.sh                 # Script de déploiement
│   ├── Makefile                  # Commandes Make
│   ├── optimize-gitbash.sh       # Optimisation Git Bash
│   ├── optimize.ps1              # Optimisation PowerShell
│   └── optimize.sh               # Optimisation Linux/Mac
│
├── 📁 src/                       # Code source PHP
│   ├── Contract/                 # Interfaces/Contrats
│   ├── Controller/               # Contrôleurs Symfony
│   ├── DataFixtures/             # Données de test
│   ├── Dto/                      # Data Transfer Objects
│   ├── Entity/                   # Entités Doctrine
│   ├── EventListener/            # Écouteurs d'événements
│   ├── Factory/                  # Factories
│   ├── Form/                     # Formulaires Symfony
│   ├── Model/                    # Modèles métier
│   ├── Repository/               # Repositories Doctrine
│   ├── Security/                 # Sécurité et authentification
│   ├── Service/                  # Services métier
│   ├── Twig/                     # Extensions Twig
│   └── Kernel.php                # Kernel Symfony
│
├── 📁 templates/                 # Templates Twig
│   ├── admin/                    # Templates administration
│   ├── base.html.twig            # Template de base
│   ├── broadcast/                # Templates broadcast
│   ├── bundles/                  # Templates de bundles
│   ├── dom/                      # Templates DOM
│   ├── home/                     # Templates accueil
│   ├── partials/                 # Partiels réutilisables
│   └── security/                 # Templates sécurité
│
├── 📁 tests/                     # Tests automatisés
│   ├── bootstrap.php             # Bootstrap des tests
│   ├── Controller/               # Tests de contrôleurs
│   ├── Functional/               # Tests fonctionnels
│   ├── Security/                 # Tests de sécurité
│   ├── Unit/                     # Tests unitaires
│   ├── js/                       # Tests JavaScript
│   ├── test_db_connection.php    # Test connexion DB
│   ├── test_db.php               # Test base de données
│   ├── test_ldap_connexion.php   # Test connexion LDAP
│   └── test_ldap.php             # Test LDAP
│
├── 📁 translations/              # Fichiers de traduction
│
├── 📁 var/                       # Fichiers variables (cache, logs)
│   ├── cache/                    # Cache Symfony
│   └── log/                      # Logs de l'application
│
├── 📁 vendor/                    # Dépendances Composer
│
├── 📄 .bashrc                    # Configuration Git Bash
├── 📄 .gitattributes             # Attributs Git
├── 📄 .gitignore                 # Fichiers ignorés par Git
├── 📄 babel.config.js            # Configuration Babel
├── 📄 composer.json              # Dépendances PHP
├── 📄 composer.lock              # Lock file Composer
├── 📄 jest.config.js             # Configuration Jest
├── 📄 jest.setup.js              # Setup Jest
├── 📄 package.json               # Dépendances Node.js
├── 📄 package-lock.json          # Lock file NPM
├── 📄 phpunit.xml.dist           # Configuration PHPUnit
├── 📄 README.md                  # Documentation principale
├── 📄 symfony.lock               # Lock file Symfony
├── 📄 tsconfig.json              # Configuration TypeScript
├── 📄 webpack.config.js          # Configuration Webpack
└── 📄 yarn.lock                  # Lock file Yarn
```

## 🎯 **Principes d'organisation**

### **📁 Séparation des responsabilités**
- **`src/`** : Code source PHP (logique métier)
- **`templates/`** : Vues et templates Twig
- **`assets/`** : Assets frontend (CSS, JS, images)
- **`config/`** : Configuration Symfony
- **`docs/`** : Documentation du projet
- **`tests/`** : Tests automatisés
- **`scripts/`** : Scripts utilitaires

### **🔧 Configuration**
- **`config/`** : Configuration Symfony centralisée
- **`public/`** : Point d'entrée web uniquement
- **`var/`** : Fichiers variables (cache, logs)

### **📚 Documentation**
- **`docs/`** : Toute la documentation du projet
- **`README.md`** : Documentation principale à la racine

### **🧪 Tests**
- **`tests/`** : Tous les tests (unitaires, fonctionnels, etc.)
- **Fichiers de test** : Déplacés depuis la racine

### **🛠️ Scripts**
- **`scripts/`** : Tous les scripts utilitaires
- **Scripts d'optimisation** : Centralisés

## 📋 **Conventions de nommage**

### **Fichiers PHP**
- **Entités** : `PascalCase.php` (ex: `User.php`)
- **Contrôleurs** : `PascalCaseController.php` (ex: `UserController.php`)
- **Services** : `PascalCaseService.php` (ex: `UserService.php`)
- **Repositories** : `PascalCaseRepository.php` (ex: `UserRepository.php`)

### **Fichiers de configuration**
- **YAML** : `snake_case.yaml` (ex: `doctrine.yaml`)
- **JSON** : `kebab-case.json` (ex: `package.json`)

### **Assets**
- **CSS/SCSS** : `kebab-case.scss` (ex: `user-profile.scss`)
- **JavaScript/TypeScript** : `camelCase.ts` (ex: `userProfile.ts`)

### **Tests**
- **PHPUnit** : `PascalCaseTest.php` (ex: `UserTest.php`)
- **Jest** : `camelCase.test.js` (ex: `userProfile.test.js`)

## 🚀 **Avantages de cette structure**

1. **🎯 Clarté** : Chaque dossier a une responsabilité claire
2. **🔍 Facilité de navigation** : Structure logique et prévisible
3. **📚 Documentation centralisée** : Tout dans `docs/`
4. **🧪 Tests organisés** : Tous les tests dans `tests/`
5. **🛠️ Scripts utilitaires** : Centralisés dans `scripts/`
6. **⚡ Performance** : Structure optimisée pour Symfony
7. **🔧 Maintenance** : Facile à maintenir et étendre
