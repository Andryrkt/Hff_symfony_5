# 📦 Guide d'Installation - HFFINTRANET

## 🎯 Objectif
Ce document décrit la procédure d'installation complète de l'application HFFINTRANET basée sur Symfony 5.

## ⚙️ Prérequis Système

### Logiciels requis
- **PHP** >= 7.4
- **Composer** >= 2.0
- **Symfony CLI** >= 5.0
- **Node.js** >= 14.0 et **Yarn** ou **npm**
- **Serveur web** : Apache 2.4+ ou Nginx 1.18+
- **Base de données** : SQL Server 2019 ou Informix

### Extensions PHP requises
```bash
# Vérifier les extensions installées
php -m

# Extensions obligatoires
- pdo_sqlsrv (pour SQL Server)
- pdo_informix (pour Informix)
- ldap
- mbstring
- xml
- curl
- zip
```

## 🚀 Installation étape par étape

### 1. Cloner le projet
```bash
git clone https://github.com/Andryrkt/Hff_symfony_5.git
cd Hff_symfony_5
```

### 2. Installer les dépendances PHP
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Installer les dépendances JavaScript
```bash
yarn install
# ou
npm install
```

### 4. Configuration de l'environnement
```bash
# Copier le fichier d'environnement
cp .env .env.local

# Éditer .env.local avec vos paramètres
nano .env.local
```

### 5. Configuration de la base de données
```yaml
# .env.local
DATABASE_URL="sqlsrv://user:password@server:1433/database_name"
# ou pour Informix
DATABASE_URL="informix://user:password@server:9088/database_name"
```

### 6. Créer la base de données
```bash
php bin/console doctrine:database:create
```

### 7. Exécuter les migrations
```bash
php bin/console doctrine:migrations:migrate
```

### 8. Charger les données de test (optionnel)
```bash
php bin/console doctrine:fixtures:load --env=dev
```

### 9. Compiler les assets
```bash
yarn encore production
# ou
npm run build
```

### 10. Configurer les permissions
```bash
# Sur Linux/Mac
chmod -R 755 var/
chmod -R 755 public/

# Sur Windows (PowerShell)
icacls var /grant "IIS_IUSRS:(OI)(CI)F"
icacls public /grant "IIS_IUSRS:(OI)(CI)F"
```

## 🔧 Configuration du serveur web

### Apache (.htaccess)
```apache
# public/.htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Nginx
```nginx
server {
    listen 80;
    server_name hffintranet.local;
    root /path/to/hff_symfony_5/public;
    
    location / {
        try_files $uri /index.php$is_args$args;
    }
    
    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }
}
```

## 🧪 Vérification de l'installation

### 1. Tester l'application
```bash
# Démarrer le serveur de développement
symfony server:start

# Ou avec PHP intégré
php -S localhost:8000 -t public/
```

### 2. Vérifier les logs
```bash
# Vérifier les logs Symfony
tail -f var/log/dev.log

# Vérifier les logs Apache/Nginx
tail -f /var/log/apache2/error.log
```

### 3. Tests automatisés
```bash
php bin/phpunit
```

## 🚨 Dépannage

### Problème courant : Extension ODBC manquante
```bash
# Vérifier si l'extension est activée
php -m | grep odbc

# Si manquante, installer sur Ubuntu/Debian
sudo apt-get install php-odbc

# Sur Windows, décommenter dans php.ini
extension=php_odbc.dll
```

### Problème : Permissions insuffisantes
```bash
# Vérifier les permissions
ls -la var/
ls -la public/

# Corriger si nécessaire
chmod -R 755 var/
chmod -R 755 public/
```

## 📞 Support
En cas de problème, consulter :
- Les logs dans `var/log/`
- La documentation Symfony : https://symfony.com/doc/5.4/
- Contacter l'équipe de développement
