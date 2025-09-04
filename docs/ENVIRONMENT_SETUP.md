# 🔧 Configuration de l'Environnement

## Variables d'environnement requises

Créez un fichier `.env` à la racine du projet avec les variables suivantes :

```bash
# Environnement de l'application
APP_ENV=dev
APP_SECRET=your-secret-key-here

# Base de données principale (SQL Server)
DATABASE_URL="sqlsrv://username:password@server:port/database_name"

# Base de données Informix
DB_DNS_INFORMIX=your-informix-dsn
DB_USERNAME_INFORMIX=your-informix-username
DB_PASSWORD_INFORMIX=your-informix-password

# Base de données SQL Server 28
DB_DNS_SQLSERV=your-sqlserver-dsn
DB_USERNAME_SQLSERV=your-sqlserver-username
DB_PASSWORD_SQLSERV=your-sqlserver-password

# LDAP Configuration
LDAP_HOST=your-ldap-host
LDAP_PORT=389
LDAP_BASE_DN=OU=HFF Users,DC=fraise,DC=hff,DC=mg
LDAP_USERNAME=your-ldap-username
LDAP_PASSWORD=your-ldap-password

# Mailer Configuration
MAILER_DSN=smtp://localhost

# Debug Configuration
SYMFONY_DEBUG=1
```

## Fichiers ignorés par Git

Le fichier `.gitignore` est configuré pour ignorer :

### 🔒 **Fichiers sensibles**
- `.env` - Variables d'environnement
- `*.key`, `*.pem`, `*.crt` - Certificats et clés
- `composer.lock` - Lock file Composer

### 📦 **Dépendances**
- `/vendor/` - Packages Composer
- `/node_modules/` - Packages NPM
- `package-lock.json`, `yarn.lock` - Lock files NPM

### 🗂️ **Cache et temporaires**
- `/var/` - Cache Symfony
- `/public/build/` - Assets compilés
- `*.log` - Fichiers de logs
- `cache/`, `tmp/`, `temp/` - Dossiers temporaires

### 💻 **IDE et éditeurs**
- `.vscode/` - Configuration VS Code
- `.idea/` - Configuration PhpStorm
- `*.sublime-*` - Configuration Sublime Text

### 🖥️ **Système d'exploitation**
- `Thumbs.db` - Windows
- `.DS_Store` - macOS
- `*~` - Linux

### 🗄️ **Base de données**
- `*.sqlite`, `*.db` - Fichiers de base de données
- `*.sql` - Dumps SQL

### 📁 **Uploads et médias**
- `/uploads/` - Fichiers uploadés
- `/media/` - Fichiers multimédias
- `/storage/` - Stockage local

## 🚀 Installation

1. **Cloner le projet** :
   ```bash
   git clone <repository-url>
   cd hff_symfony_5
   ```

2. **Installer les dépendances** :
   ```bash
   composer install
   npm install
   ```

3. **Configurer l'environnement** :
   ```bash
   cp .env.example .env
   # Éditer .env avec vos vraies valeurs
   ```

4. **Initialiser la base de données** :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Compiler les assets** :
   ```bash
   npm run build
   ```

## ⚠️ Sécurité

- **Ne jamais commiter** le fichier `.env`
- **Ne jamais commiter** les certificats ou clés privées
- **Utiliser des variables d'environnement** pour les secrets
- **Configurer les permissions** appropriées sur les serveurs
