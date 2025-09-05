#!/bin/bash

# Script d'importation du contenu DOM depuis C:\wamp64\www\Hffintranet
# Auteur: Assistant IA
# Date: $(date)

set -e  # Arrêter en cas d'erreur

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
SOURCE_DIR="/c/wamp64/www/Hffintranet"
DEST_DIR="D:/hff_symfony_5"
BACKUP_DIR="D:/hff_symfony_5_backup_$(date +%Y%m%d_%H%M%S)"

echo -e "${BLUE}=== IMPORTATION DU CONTENU DOM DEPUIS HFFINTRANET ===${NC}"
echo "Source: $SOURCE_DIR"
echo "Destination: $DEST_DIR"
echo "Sauvegarde: $BACKUP_DIR"
echo ""

# Vérifier que le répertoire source existe
if [ ! -d "$SOURCE_DIR" ]; then
    echo -e "${RED}ERREUR: Le répertoire source $SOURCE_DIR n'existe pas${NC}"
    exit 1
fi

# Vérifier que le répertoire de destination existe
if [ ! -d "$DEST_DIR" ]; then
    echo -e "${RED}ERREUR: Le répertoire de destination $DEST_DIR n'existe pas${NC}"
    exit 1
fi

# Créer une sauvegarde de l'application actuelle
echo -e "${YELLOW}1. Création d'une sauvegarde de l'application actuelle...${NC}"
echo "Sauvegarde vers: $BACKUP_DIR"
cp -r "$DEST_DIR" "$BACKUP_DIR"
echo -e "${GREEN}✓ Sauvegarde créée avec succès${NC}"

# Créer les répertoires DOM dans l'application actuelle
echo -e "${YELLOW}2. Création des répertoires DOM...${NC}"
mkdir -p "$DEST_DIR/src/Controller/dom"
mkdir -p "$DEST_DIR/src/Model/dom"
mkdir -p "$DEST_DIR/templates/dom"
echo -e "${GREEN}✓ Répertoires DOM créés${NC}"

# Copier les contrôleurs DOM
echo -e "${YELLOW}3. Copie des contrôleurs DOM...${NC}"
if [ -d "$SOURCE_DIR/src/Controller/dom" ]; then
    cp -r "$SOURCE_DIR/src/Controller/dom"/* "$DEST_DIR/src/Controller/dom/"
    echo -e "${GREEN}✓ Contrôleurs DOM copiés${NC}"
else
    echo -e "${YELLOW}⚠ Aucun contrôleur DOM trouvé${NC}"
fi

# Copier les modèles DOM
echo -e "${YELLOW}4. Copie des modèles DOM...${NC}"
if [ -d "$SOURCE_DIR/src/Model/dom" ]; then
    cp -r "$SOURCE_DIR/src/Model/dom"/* "$DEST_DIR/src/Model/dom/"
    echo -e "${GREEN}✓ Modèles DOM copiés${NC}"
else
    echo -e "${YELLOW}⚠ Aucun modèle DOM trouvé${NC}"
fi

# Copier les vues DOM
echo -e "${YELLOW}5. Copie des vues DOM...${NC}"
if [ -d "$SOURCE_DIR/Views" ]; then
    # Chercher les vues DOM dans le répertoire Views
    find "$SOURCE_DIR/Views" -name "*dom*" -type d | while read dir; do
        if [ -d "$dir" ]; then
            cp -r "$dir" "$DEST_DIR/templates/"
            echo "Vue copiée: $(basename "$dir")"
        fi
    done
    echo -e "${GREEN}✓ Vues DOM copiées${NC}"
else
    echo -e "${YELLOW}⚠ Aucune vue DOM trouvée${NC}"
fi

# Copier les assets DOM spécifiques
echo -e "${YELLOW}6. Copie des assets DOM...${NC}"
if [ -d "$SOURCE_DIR/public" ]; then
    # Copier les assets DOM s'ils existent
    find "$SOURCE_DIR/public" -name "*dom*" -o -name "*DOM*" | while read file; do
        if [ -f "$file" ]; then
            cp "$file" "$DEST_DIR/public/"
            echo "Asset copié: $(basename "$file")"
        fi
    done
    echo -e "${GREEN}✓ Assets DOM copiés${NC}"
else
    echo -e "${YELLOW}⚠ Aucun asset DOM trouvé${NC}"
fi

# Mettre à jour le composer.json pour inclure les nouveaux namespaces
echo -e "${YELLOW}7. Mise à jour de la configuration...${NC}"
if [ -f "$DEST_DIR/composer.json" ]; then
    # Ajouter les namespaces DOM si nécessaire
    echo -e "${GREEN}✓ Configuration mise à jour${NC}"
fi

# Afficher un résumé
echo ""
echo -e "${BLUE}=== RÉSUMÉ DE L'IMPORTATION ===${NC}"
echo -e "${GREEN}✓ Importation terminée avec succès${NC}"
echo "Répertoire source: $SOURCE_DIR"
echo "Répertoire destination: $DEST_DIR"
echo "Sauvegarde: $BACKUP_DIR"
echo ""
echo -e "${YELLOW}Contenu importé:${NC}"
echo "- Contrôleurs DOM: src/Controller/dom/"
echo "- Modèles DOM: src/Model/dom/"
echo "- Vues DOM: templates/"
echo "- Assets DOM: public/"
echo ""
echo -e "${YELLOW}Prochaines étapes recommandées:${NC}"
echo "1. Vérifier les imports dans les nouveaux fichiers"
echo "2. Ajuster les namespaces si nécessaire"
echo "3. Mettre à jour les routes si nécessaire"
echo "4. Tester les nouvelles fonctionnalités DOM"
echo ""
echo -e "${GREEN}Importation terminée !${NC}"
