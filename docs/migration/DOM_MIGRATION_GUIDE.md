# Guide de Migration des Données DOM

Ce guide explique comment migrer les données de l'ancienne table `Demande_ordre_mission` vers la nouvelle structure de l'entité `Dom`.

## Prérequis

1. **Accès aux deux bases de données** : Vous devez avoir accès à l'ancienne et à la nouvelle base de données SQL Server
2. **Symfony configuré** : L'application Symfony doit être correctement configurée

## Configuration

### 1. Configurer la connexion à l'ancienne base de données

Copiez le fichier `.env.migration` vers `.env.local` et modifiez la variable `DATABASE_LEGACY_URL` :

```bash
# Dans .env.local
DATABASE_LEGACY_URL=sqlsrv://utilisateur:motdepasse@serveur:port/ancienne_base_donnees
```

**Exemples** :
- Même serveur, base différente : `sqlsrv://sa:password@localhost:1433/hff_old`
- Serveur différent : `sqlsrv://user:pass@192.168.1.100:1433/ancienne_base`

### 2. Vérifier la configuration

Testez la connexion à l'ancienne base :

```bash
php bin/console dbal:run-sql "SELECT COUNT(*) FROM Demande_ordre_mission" --connection=legacy
```

Si la commande retourne un nombre, la connexion fonctionne correctement.

## Utilisation de la Commande de Migration

### Test en Mode Dry-Run (Recommandé)

Avant de migrer réellement les données, testez avec quelques enregistrements :

```bash
# Test avec 10 enregistrements sans modifier la base
php bin/console app:migrate:dom-data --dry-run --limit=10
```

Cette commande :
- Lit 10 enregistrements de l'ancienne base
- Effectue toutes les transformations
- **N'écrit RIEN dans la nouvelle base**
- Affiche les statistiques

### Migration Complète

Une fois le test réussi, lancez la migration complète :

```bash
# Migration de toutes les données
php bin/console app:migrate:dom-data
```

### Options Disponibles

| Option | Description | Exemple |
|--------|-------------|---------|
| `--dry-run` | Test sans écriture dans la base | `--dry-run` |
| `--batch-size=N` | Nombre d'enregistrements par lot (défaut: 100) | `--batch-size=50` |
| `--limit=N` | Limite le nombre d'enregistrements à migrer | `--limit=1000` |
| `--offset=N` | Commence à partir de l'enregistrement N | `--offset=500` |

### Exemples d'Utilisation

```bash
# Test avec 50 enregistrements
php bin/console app:migrate:dom-data --dry-run --limit=50

# Migration par lots de 200
php bin/console app:migrate:dom-data --batch-size=200

# Reprendre une migration à partir du 1000ème enregistrement
php bin/console app:migrate:dom-data --offset=1000

# Migration des 5000 premiers enregistrements par lots de 100
php bin/console app:migrate:dom-data --limit=5000 --batch-size=100

# Option 1: Avec limite de mémoire augmentée
php -d memory_limit=512M bin/console app:migrate:dom-data --batch-size=20 --limit=100

# Option 2: Migration par petits lots
php bin/console app:migrate:dom-data --batch-size=20 --limit=50

# Option 3: Migration complète avec petits lots
php -d memory_limit=512M bin/console app:migrate:dom-data --batch-size=20
```

## Mapping des Données

### Colonnes Migrées

Toutes les colonnes compatibles sont automatiquement migrées :
- Informations de base (numéro, matricule, nom, etc.)
- Dates et heures
- Montants et indemnités
- Pièces jointes
- Relations (StatutDemande, SousTypeDocument, Site, Categorie, Agence, Service)

### Colonnes Non Migrées (Perdues)

Les colonnes suivantes de l'ancien schéma **ne sont pas migrées** :
- `Type_Document`
- `Autre_Type_Document`
- `Code_AgenceService_Debiteur`
- `Numero_OR`
- `Date_CPT`, `Date_PAY`, `Date_ANN`
- `Utilisateur_Creation`, `Utilisateur_Modification`, `Date_Modif` (remplacés par TimestampableTrait)

### Transformations Appliquées

1. **Dates** : `datetime` → `date` (perte de l'heure pour Date_Demande, Date_Debut, Date_Fin)
2. **Relations Agence/Service** : Les relations émetteur sont utilisées pour remplir `agenceEmetteurId` et `serviceEmetteurId`
3. **Nombre de jours** : `integer` → `smallint`

## Vérification Post-Migration

### 1. Vérifier le nombre d'enregistrements

```bash
# Ancienne base
php bin/console dbal:run-sql "SELECT COUNT(*) FROM Demande_ordre_mission" --connection=legacy

# Nouvelle base
php bin/console dbal:run-sql "SELECT COUNT(*) FROM Demande_ordre_mission"
```

Les deux nombres doivent correspondre (ou être proches si vous avez utilisé `--limit`).

### 2. Vérifier quelques enregistrements manuellement

Comparez quelques enregistrements entre l'ancienne et la nouvelle base pour vous assurer que les données sont correctement migrées.

### 3. Consulter les logs

Les erreurs et avertissements sont enregistrés dans un fichier de log dédié :

```bash
# Voir les logs de migration en temps réel
tail -f var/log/migrations/migration_dom.log

# Voir tous les logs de migration
cat var/log/migrations/migration_dom.log
```

## Rapport de Migration

À la fin de la migration, la commande affiche un rapport détaillé :

```
Statistiques de migration
┌─────────────┬────────┐
│ Métrique    │ Valeur │
├─────────────┼────────┤
│ Total traité│ 1000   │
│ Succès      │ 995    │
│ Erreurs     │ 3      │
│ Ignorés     │ 2      │
└─────────────┴────────┘
Taux de réussite: 99.50%
```

## Gestion des Erreurs

### Erreurs Courantes

1. **Connexion impossible à l'ancienne base**
   - Vérifiez `DATABASE_LEGACY_URL` dans `.env.local`
   - Vérifiez que le serveur SQL Server est accessible

2. **Relation introuvable** (ex: StatutDemande, SousTypeDocument)
   - Assurez-vous que les tables de référence sont migrées en premier
   - Vérifiez que les IDs existent dans la nouvelle base

3. **Erreur de validation Doctrine**
   - Consultez les logs pour identifier le champ problématique
   - Vérifiez que les contraintes de l'entité Dom sont respectées

### En Cas de Problème

1. **Consultez les logs** : `var/log/dev.log`
2. **Utilisez --dry-run** pour tester sans risque
3. **Utilisez --limit** pour migrer par petits lots
4. **Utilisez --offset** pour reprendre une migration interrompue

## Performance

Pour de gros volumes de données (> 10 000 enregistrements) :

1. **Augmentez la taille des lots** : `--batch-size=500`
2. **Désactivez les logs de debug** en production
3. **Exécutez en environnement de production** : `APP_ENV=prod php bin/console app:migrate:dom-data`

## Après la Migration

Une fois la migration terminée avec succès :

1. **Vérifiez les données** dans la nouvelle base
2. **Testez l'application** avec les données migrées
3. **Supprimez la connexion legacy** de `doctrine.yaml` (optionnel)
4. **Supprimez `DATABASE_LEGACY_URL`** de `.env.local` (optionnel)

## Support

En cas de problème, consultez :
- Les logs de migration : `var/log/migrations/migration_dom.log`
- Le code source : `src/Command/Migration/MigrateDomDataCommand.php`
- Le mapper : `src/Service/Migration/DomMigrationMapper.php`
