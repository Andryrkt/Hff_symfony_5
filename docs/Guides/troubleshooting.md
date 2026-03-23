# 🔧 Guide de Dépannage (Troubleshooting)

Ce guide recense les problèmes courants rencontrés lors du développement ou du déploiement, et leurs solutions.

## 🕸️ Problèmes Web / Routage

### Erreur 404 "Route not found" sur la documentation
**Symptôme** : Vous cliquez sur un lien et tombez sur une erreur 404 Symfony.
**Cause** : Le fichier Markdown pointé n'existe pas ou le chemin est incorrect (ex: tentative de remonter dans l'arborescence avec `..`).
**Solution** :
- Vérifier que le fichier `.md` existe bien dans le dossier `docs/`.
- Pour les liens vers le README racine, utiliser le lien spécial : `[Lien](project_readme.md)`.

### Erreur 500 "Class not found" après création d'un contrôleur
**Cause** : L'autoloader de Composer n'est pas à jour ou le namespace est incorrect.
**Solution** :
```bash
composer dump-autoload
```

## 🗄️ Problèmes Base de Données

### "Connection refused" ou "Login failed"
**Cause** : Mauvais identifiants dans le fichier `.env` ou `.env.local`.
**Solution** : Vérifiez la variable `DATABASE_URL`. Attention aux caractères spéciaux dans le mot de passe (ils doivent être encodés en URL, ex: `#` devient `%23`).

### Erreur lors des migrations "Table already exists"
**Cause** : La base de données et les fichiers de migration sont désynchronisés.
**Solution** :
Si vous êtes en **dev** et pouvez perdre les données :
```bash
php bin/console doctrine:schema:drop --force
php bin/console doctrine:migrations:migrate
```

## 📦 Problèmes Frontend / Assets

### "Webpack Encore" ou "require is not defined"
**Cause** : Les assets n'ont pas été compilés ou il manque des dépendances Node.
**Solution** :
```bash
npm install
npm run dev
# ou pour la prod
npm run build
```

### Le style CSS ne change pas malgré mes modifications
**Cause** : Cache navigateur ou cache Symfony.
**Solution** :
- Forcer le rechargement de la page (Ctrl+F5).
- Vider le cache Symfony : `php bin/console c:c`.

## 🧠 Problèmes Cache & Performance

### Changements non pris en compte (Twig, Config)
**C'est le classique "C'est le cache !".**
**Solution** :
Toujours avoir ce réflexe en premier :
```bash
php bin/console cache:clear
```
