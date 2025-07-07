# ⚙️ Guide de Configuration - HFFINTRANET

## 🎯 Objectif
Ce document détaille la configuration complète de l'application HFFINTRANET, incluant les paramètres de base de données, LDAP, sécurité et environnement.

## 🔧 Configuration des environnements

### Variables d'environnement principales

#### .env.local (Production)
```yaml
# Base de données SQL Server
DATABASE_URL="sqlsrv://username:password@server:1433/hffintranet"

# Base de données Informix (alternative)
# DATABASE_URL="informix://username:password@server:9088/hffintranet"

# Configuration LDAP
LDAP_HOST="ldap://ldap.company.com"
LDAP_PORT=389
LDAP_BASE_DN="DC=company,DC=com"
LDAP_USER_DN="CN=service_account,OU=ServiceAccounts,DC=company,DC=com"
LDAP_PASSWORD="service_password"

# Configuration de sécurité
APP_SECRET="votre_secret_tres_long_et_complexe"
SECURITY_FIREWALLS_MAIN_PATTERN="^/"
SECURITY_FIREWALLS_MAIN_PROVIDERS="ldap_provider"

# Configuration des logs
MONOLOG_CHANNELS=["!event", "!doctrine"]
MONOLOG_LEVEL=INFO

# Configuration du cache
CACHE_ADAPTER="cache.adapter.filesystem"
CACHE_DEFAULT_TTL=3600
```

#### .env.test (Tests)
```yaml
# Base de données de test
DATABASE_URL="sqlite:///%kernel.project_dir%/var/test.db"

# Configuration LDAP de test
LDAP_HOST="ldap://test-ldap.company.com"
LDAP_PORT=389
LDAP_BASE_DN="DC=test,DC=company,DC=com"

# Désactiver les logs pour les tests
MONOLOG_LEVEL=ERROR
```

## 🔐 Configuration de la sécurité

### config/packages/security.yaml
```yaml
security:
    providers:
        ldap_provider:
            ldap:
                service: Symfony\Component\Ldap\Ldap
                base_dn: '%env(LDAP_BASE_DN)%'
                search_dn: '%env(LDAP_USER_DN)%'
                search_password: '%env(LDAP_PASSWORD)%'
                default_roles: ['ROLE_USER']
                uid_key: 'sAMAccountName'
    
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            pattern: ^/
            provider: ldap_provider
            custom_authenticator: App\Security\LdapAuthenticator
            logout:
                path: app_logout
                target: app_login
    
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/api, roles: ROLE_API }
        - { path: ^/user, roles: ROLE_USER }
```

### Configuration des rôles et permissions
```yaml
# config/packages/security.yaml (suite)
    role_hierarchy:
        ROLE_ADMIN: [ROLE_USER, ROLE_MANAGER]
        ROLE_MANAGER: [ROLE_USER]
        ROLE_ATELIER: [ROLE_USER]
        ROLE_MAGASIN: [ROLE_USER]
        ROLE_RH: [ROLE_USER]
        ROLE_APPRO: [ROLE_USER]
```

## 🗄️ Configuration de la base de données

### config/packages/doctrine.yaml
```yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
        options:
            # Configuration SQL Server
            TrustServerCertificate: true
            Encrypt: false
            # Configuration Informix
            # INFORMIXDIR: '/opt/informix'
            # INFORMIXSERVER: 'ol_informix'
    
    orm:
        auto_generate_proxy_classes: true
        enable_lazy_ghost_objects: true
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true
        mappings:
            App:
                is_bundle: false
                dir: '%kernel.project_dir%/src/Entity'
                prefix: 'App\Entity'
                alias: App
```

### Configuration des migrations
```yaml
# config/packages/doctrine_migrations.yaml
doctrine_migrations:
    migrations_paths:
        'DoctrineMigrations': '%kernel.project_dir%/migrations'
    enable_profiler: false
```

## 📧 Configuration du mailer

### config/packages/mailer.yaml
```yaml
framework:
    mailer:
        dsn: '%env(MAILER_DSN)%'
        envelope:
            sender: 'noreply@hff.mg'
            recipients: ['admin@hff.mg']
```

### Exemples de configuration mailer
```yaml
# .env.local
# Pour SMTP interne
MAILER_DSN=smtp://user:pass@smtp.company.com:587

# Pour Gmail
MAILER_DSN=gmail://user:app_password@default

# Pour SendGrid
MAILER_DSN=sendgrid://KEY@default
```

## 🔍 Configuration des logs

### config/packages/monolog.yaml
```yaml
monolog:
    channels: ['!event', '!doctrine']
    handlers:
        main:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.log"
            level: "%env(MONOLOG_LEVEL)%"
            channels: ["!event"]
        nested:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.log"
            level: debug
            channels: ["!event"]
        console:
            type: console
            process_psr_3_messages: false
            channels: ["!event", "!doctrine", "!console"]
        deprecation:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.deprecations.log"
        deprecation_filter:
            type: filter
            handler: deprecation
            max_level: info
            channels: ["php"]
```

## 🎨 Configuration des assets

### webpack.config.js
```javascript
const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .addEntry('login', './assets/js/login/login.js')
    .enableStimulusController('./assets/controllers/')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]',
    })
;

module.exports = Encore.getWebpackConfig();
```

## 🌐 Configuration du cache

### config/packages/cache.yaml
```yaml
framework:
    cache:
        app: cache.adapter.filesystem
        system: cache.adapter.system
        directory: '%kernel.cache_dir%/pools'
        default_doctrine_provider: 'doctrine.system_cache_pool'
        pools:
            cache.doctrine.orm.default.result:
                adapter: cache.adapter.filesystem
                tags: cache.tags.filesystem
            cache.doctrine.orm.default.query:
                adapter: cache.adapter.filesystem
                tags: cache.tags.filesystem
```

## 🔧 Configuration des services

### config/services.yaml
```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: false

    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'

    # Services personnalisés
    App\Service\LdapService:
        arguments:
            $host: '%env(LDAP_HOST)%'
            $port: '%env(LDAP_PORT)%'
            $baseDn: '%env(LDAP_BASE_DN)%'
            $userDn: '%env(LDAP_USER_DN)%'
            $password: '%env(LDAP_PASSWORD)%'

    App\Service\Menu\MenuBuilder:
        tags:
            - { name: 'twig.extension' }
```

## 🚀 Configuration de production

### Optimisations recommandées
```bash
# Vider le cache
php bin/console cache:clear --env=prod

# Compiler les assets
yarn encore production

# Optimiser l'autoloader
composer dump-autoload --optimize --no-dev --classmap-authoritative

# Vérifier les permissions
chmod -R 755 var/
chmod -R 755 public/
```

## 📊 Configuration de monitoring

### config/packages/web_profiler.yaml (développement uniquement)
```yaml
web_profiler:
    toolbar: true
    intercept_redirects: false

framework:
    profiler:
        only_exceptions: false
        collect: true
```

## 🔍 Vérification de la configuration

### Commandes de vérification
```bash
# Vérifier la configuration
php bin/console debug:config

# Vérifier les routes
php bin/console debug:router

# Vérifier les services
php bin/console debug:container

# Vérifier la base de données
php bin/console doctrine:schema:validate

# Tester la connexion LDAP
php bin/console app:test-ldap
```

## 📞 Support technique
Pour toute question sur la configuration :
- Consulter les logs dans `var/log/`
- Vérifier la documentation Symfony
- Contacter l'équipe de développement 