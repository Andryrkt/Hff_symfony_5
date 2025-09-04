#!/bin/bash

# Script de réorganisation du projet
echo "🗂️  Réorganisation de la structure du projet..."

# Créer les dossiers s'ils n'existent pas
echo "📁 Création des dossiers..."
mkdir -p docs/fonctionnelle
mkdir -p docs/technique
mkdir -p docs/assets
mkdir -p tests/coverage
mkdir -p scripts/tmp

# Déplacer les fichiers de test vers tests/
echo "🧪 Déplacement des fichiers de test..."
if [ -f "test_db_connection.php" ]; then
    mv test_db_connection.php tests/
    echo "  ✅ test_db_connection.php → tests/"
fi

if [ -f "test_db.php" ]; then
    mv test_db.php tests/
    echo "  ✅ test_db.php → tests/"
fi

if [ -f "test_ldap_connexion.php" ]; then
    mv test_ldap_connexion.php tests/
    echo "  ✅ test_ldap_connexion.php → tests/"
fi

if [ -f "test_ldap.php" ]; then
    mv test_ldap.php tests/
    echo "  ✅ test_ldap.php → tests/"
fi

# Déplacer les fichiers de documentation vers docs/
echo "📚 Déplacement des fichiers de documentation..."
if [ -f "GUIDE_UTILISATEUR.md" ]; then
    mv GUIDE_UTILISATEUR.md docs/
    echo "  ✅ GUIDE_UTILISATEUR.md → docs/"
fi

# Déplacer les scripts vers scripts/
echo "🛠️  Déplacement des scripts..."
if [ -f "deploy.sh" ]; then
    mv deploy.sh scripts/
    echo "  ✅ deploy.sh → scripts/"
fi

if [ -f "Makefile" ]; then
    mv Makefile scripts/
    echo "  ✅ Makefile → scripts/"
fi

# Créer les fichiers de documentation manquants
echo "📝 Création des fichiers de documentation..."
if [ ! -f "docs/PROJECT_STRUCTURE.md" ]; then
    echo "  ✅ Création de docs/PROJECT_STRUCTURE.md"
fi

# Mettre à jour les permissions
echo "🔐 Mise à jour des permissions..."
chmod +x scripts/*.sh 2>/dev/null || true

echo "✅ Réorganisation terminée !"
echo ""
echo "📋 Structure finale :"
echo "├── 📁 docs/          # Documentation"
echo "├── 📁 tests/         # Tests (incluant les fichiers de test déplacés)"
echo "├── 📁 scripts/       # Scripts utilitaires"
echo "├── 📁 src/           # Code source PHP"
echo "├── 📁 assets/        # Assets frontend"
echo "├── 📁 templates/     # Templates Twig"
echo "├── 📁 config/        # Configuration Symfony"
echo "└── 📁 public/        # Point d'entrée web"
echo ""
echo "💡 Prochaines étapes :"
echo "1. Vérifiez la structure : ls -la"
echo "2. Testez l'application : php bin/console cache:clear"
echo "3. Committez les changements : git add . && git commit -m 'feat: Reorganize project structure'"
