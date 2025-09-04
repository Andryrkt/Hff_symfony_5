#!/bin/bash

# Script pour nettoyer les fichiers qui ne devraient pas être versionnés
echo "🧹 Nettoyage des fichiers Git..."

# Supprimer les fichiers sensibles du tracking Git
echo "🔒 Suppression des fichiers sensibles..."
git rm --cached .env 2>/dev/null || true
git rm --cached .env.local 2>/dev/null || true
git rm --cached .env.local.php 2>/dev/null || true

# Supprimer les fichiers de cache
echo "🗑️  Suppression des fichiers de cache..."
git rm -r --cached var/ 2>/dev/null || true
git rm -r --cached public/build/ 2>/dev/null || true

# Supprimer les dépendances
echo "📦 Suppression des dépendances..."
git rm -r --cached vendor/ 2>/dev/null || true
git rm -r --cached node_modules/ 2>/dev/null || true

# Supprimer les fichiers IDE
echo "💻 Suppression des fichiers IDE..."
git rm -r --cached .vscode/ 2>/dev/null || true
git rm -r --cached .idea/ 2>/dev/null || true

# Supprimer les fichiers système
echo "🖥️  Suppression des fichiers système..."
git rm --cached Thumbs.db 2>/dev/null || true
git rm --cached .DS_Store 2>/dev/null || true
git rm --cached Desktop.ini 2>/dev/null || true

# Supprimer les logs
echo "📝 Suppression des logs..."
git rm --cached *.log 2>/dev/null || true
git rm -r --cached logs/ 2>/dev/null || true

# Supprimer les fichiers de backup
echo "💾 Suppression des fichiers de backup..."
git rm --cached *.bak 2>/dev/null || true
git rm --cached *.backup 2>/dev/null || true
git rm --cached *.old 2>/dev/null || true

# Supprimer les fichiers temporaires
echo "⏰ Suppression des fichiers temporaires..."
git rm -r --cached tmp/ 2>/dev/null || true
git rm -r --cached temp/ 2>/dev/null || true
git rm -r --cached cache/ 2>/dev/null || true

# Supprimer les fichiers de base de données
echo "🗄️  Suppression des fichiers de base de données..."
git rm --cached *.sqlite 2>/dev/null || true
git rm --cached *.sqlite3 2>/dev/null || true
git rm --cached *.db 2>/dev/null || true

# Supprimer les fichiers de test
echo "🧪 Suppression des fichiers de test..."
git rm --cached .phpunit.result.cache 2>/dev/null || true
git rm -r --cached coverage/ 2>/dev/null || true

echo "✅ Nettoyage terminé !"
echo "📋 Fichiers modifiés :"
git status --porcelain

echo ""
echo "💡 Prochaines étapes :"
echo "1. Vérifiez les fichiers avec : git status"
echo "2. Ajoutez les fichiers souhaités : git add <fichier>"
echo "3. Committez les changements : git commit -m 'Update .gitignore and clean repository'"
echo "4. Poussez vers GitHub : git push"
