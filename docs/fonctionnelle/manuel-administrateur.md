# 👨‍💼 Manuel Administrateur - HFFINTRANET

## 🎯 Introduction

### Rôle de l'administrateur
L'administrateur HFFINTRANET est responsable de la gestion globale de l'application, incluant les utilisateurs, les agences, les services et la configuration système.

### Responsabilités principales
- ✅ Gestion des comptes utilisateurs
- ✅ Configuration des agences et services
- ✅ Gestion des permissions et rôles
- ✅ Monitoring et maintenance
- ✅ Support technique
- ✅ Sauvegarde et sécurité

### Accès administrateur
- **URL** : https://hffintranet.local/admin
- **Identifiants** : Compte avec rôle ROLE_ADMIN
- **Permissions** : Accès complet à toutes les fonctionnalités

## 🔐 Gestion des utilisateurs

### Créer un nouvel utilisateur

#### Étapes détaillées
1. **Accéder à la section "Gestion utilisateurs"**
2. **Cliquer sur "Nouvel utilisateur"**
3. **Remplir le formulaire** :
   - **Email** : Adresse email professionnelle (obligatoire)
   - **Nom complet** : Nom et prénom
   - **Téléphone** : Numéro de contact
   - **Agence** : Agence principale de l'utilisateur
   - **Service** : Service principal
   - **Rôles** : Sélectionner les rôles appropriés
4. **Configurer les permissions** :
   - **Agences autorisées** : Agences accessibles
   - **Services autorisés** : Services accessibles
   - **Groupes** : Groupes d'appartenance
5. **Cliquer sur "Créer"**

#### Rôles disponibles
- **ROLE_USER** : Utilisateur standard
- **ROLE_MANAGER** : Manager (peut approuver des demandes)
- **ROLE_ADMIN** : Administrateur (accès complet)
- **ROLE_ATELIER** : Groupe Atelier
- **ROLE_MAGASIN** : Groupe Magasin
- **ROLE_RH** : Groupe RH
- **ROLE_APPRO** : Groupe Approvisionnement

#### Exemple de création
```
Email : john.doe@hff.mg
Nom complet : John Doe
Téléphone : +261 32 123 456
Agence : Agence Centrale
Service : Service Informatique
Rôles : ROLE_USER, ROLE_ATELIER
Agences autorisées : Agence Centrale
Services autorisées : Service Informatique
```

### Modifier un utilisateur existant

#### Étapes
1. **Rechercher l'utilisateur** dans la liste
2. **Cliquer sur "Modifier"**
3. **Modifier les informations** nécessaires
4. **Sauvegarder les changements**

#### Informations modifiables
- Informations personnelles
- Rôles et permissions
- Agences et services autorisés
- Statut (actif/inactif)

### Désactiver un utilisateur

#### Procédure
1. **Trouver l'utilisateur** dans la liste
2. **Cliquer sur "Désactiver"**
3. **Confirmer l'action**
4. **Ajouter un motif** (optionnel)

#### Conséquences de la désactivation
- L'utilisateur ne peut plus se connecter
- Ses demandes restent visibles
- Ses permissions sont conservées (pour l'historique)

### Gestion des permissions avancées

#### Permissions granulaires
- **CREATE** : Créer des demandes
- **READ** : Consulter les demandes
- **UPDATE** : Modifier les demandes
- **DELETE** : Supprimer les demandes
- **APPROVE** : Approuver des demandes

#### Attribution par agence/service
```
Utilisateur : Marie Dupont
Agence : Agence Centrale
Permissions :
- Service Informatique : CREATE, READ, APPROVE
- Service RH : READ
- Service Appro : READ
```

## 🏢 Gestion des agences

### Créer une nouvelle agence

#### Étapes
1. **Accéder à "Gestion agences"**
2. **Cliquer sur "Nouvelle agence"**
3. **Remplir les informations** :
   - **Nom** : Nom de l'agence
   - **Code** : Code unique (3-5 caractères)
   - **Adresse** : Adresse complète
   - **Téléphone** : Numéro de contact
   - **Email** : Adresse email
   - **Responsable** : Nom du responsable
4. **Cliquer sur "Créer"**

#### Exemple
```
Nom : Agence de Toamasina
Code : TAM
Adresse : 123 Avenue de l'Indépendance, Toamasina
Téléphone : +261 53 123 456
Email : tam@hff.mg
Responsable : Jean Rakoto
```

### Modifier une agence

#### Informations modifiables
- Coordonnées (adresse, téléphone, email)
- Responsable
- Statut (active/inactive)
- Services rattachés

### Désactiver une agence

#### Procédure
1. **Sélectionner l'agence**
2. **Cliquer sur "Désactiver"**
3. **Confirmer l'action**

#### Impact
- Les utilisateurs de cette agence ne peuvent plus créer de demandes
- Les demandes existantes restent visibles
- Les services rattachés sont désactivés

## 🔧 Gestion des services

### Créer un nouveau service

#### Étapes
1. **Accéder à "Gestion services"**
2. **Cliquer sur "Nouveau service"**
3. **Remplir le formulaire** :
   - **Nom** : Nom du service
   - **Code** : Code unique
   - **Description** : Description du service
   - **Agence** : Agence de rattachement
   - **Responsable** : Responsable du service
4. **Cliquer sur "Créer"**

#### Exemple
```
Nom : Service Maintenance
Code : MAINT
Description : Service de maintenance des équipements
Agence : Agence Centrale
Responsable : Pierre Ravelojaona
```

### Rattacher un service à une agence

#### Procédure
1. **Modifier le service**
2. **Sélectionner l'agence** dans la liste
3. **Sauvegarder**

#### Règles de rattachement
- Un service ne peut appartenir qu'à une seule agence
- Les utilisateurs du service héritent des permissions de l'agence
- Les demandes sont filtrées par agence/service

## 📊 Monitoring et statistiques

### Tableau de bord administrateur

#### Métriques principales
- **Utilisateurs actifs** : Nombre de connexions aujourd'hui
- **Demandes en cours** : Demandes non finalisées
- **Demandes en attente** : Demandes en attente d'approbation
- **Temps de traitement** : Durée moyenne d'approbation

#### Graphiques
- **Demandes par agence** : Répartition géographique
- **Demandes par service** : Répartition par service
- **Évolution mensuelle** : Tendances des demandes
- **Taux d'approbation** : Pourcentage de demandes approuvées

### Rapports détaillés

#### Rapport utilisateurs
- Liste des utilisateurs actifs/inactifs
- Permissions par utilisateur
- Activité récente
- Demandes par utilisateur

#### Rapport agences
- Performance par agence
- Nombre de demandes
- Temps de traitement
- Taux d'approbation

#### Rapport services
- Charge de travail par service
- Types de demandes
- Responsables
- Statistiques de performance

### Export des données

#### Formats disponibles
- **Excel (.xlsx)** : Pour analyses détaillées
- **PDF** : Pour rapports officiels
- **CSV** : Pour intégration avec d'autres outils

#### Périodes d'export
- **Jour** : Données du jour
- **Semaine** : Données de la semaine
- **Mois** : Données du mois
- **Année** : Données de l'année
- **Personnalisé** : Période spécifique

## 🔧 Configuration système

### Paramètres généraux

#### Configuration LDAP
- **Serveur LDAP** : Adresse du serveur
- **Port** : Port de connexion (généralement 389)
- **Base DN** : Base de recherche
- **Compte service** : Compte de connexion
- **Mot de passe** : Mot de passe du compte service

#### Configuration email
- **Serveur SMTP** : Serveur d'envoi d'emails
- **Port** : Port SMTP
- **Authentification** : Identifiants SMTP
- **Expéditeur** : Adresse d'expédition

#### Configuration de sécurité
- **Durée de session** : Temps avant déconnexion automatique
- **Complexité mot de passe** : Règles de complexité
- **Tentatives de connexion** : Nombre maximum d'échecs
- **Verrouillage compte** : Durée de verrouillage

### Sauvegarde et maintenance

#### Sauvegarde automatique
- **Fréquence** : Quotidienne
- **Rétention** : 30 jours
- **Emplacement** : Serveur de sauvegarde sécurisé
- **Contenu** : Base de données + fichiers uploadés

#### Maintenance préventive
- **Nettoyage des logs** : Suppression des anciens logs
- **Optimisation base** : Défragmentation et optimisation
- **Vérification intégrité** : Contrôle de cohérence des données

#### Procédures de maintenance
```bash
# Nettoyage des logs
php bin/console app:clean-logs --days=30

# Optimisation de la base
php bin/console doctrine:query:sql "OPTIMIZE TABLE demandes"

# Vérification d'intégrité
php bin/console app:check-integrity
```

## 🚨 Gestion des incidents

### Types d'incidents

#### Problèmes de connexion
- **Utilisateur ne peut pas se connecter**
  - Vérifier les identifiants LDAP
  - Contrôler le statut du compte
  - Vérifier les permissions

- **Erreur de serveur**
  - Vérifier les logs d'erreur
  - Contrôler l'espace disque
  - Vérifier la connectivité réseau

#### Problèmes de données
- **Données manquantes**
  - Vérifier les sauvegardes
  - Contrôler l'intégrité de la base
  - Restaurer si nécessaire

- **Données incohérentes**
  - Identifier la source du problème
  - Corriger les données
  - Prévenir la récurrence

### Procédures d'urgence

#### Incident majeur
1. **Évaluer l'impact** : Nombre d'utilisateurs affectés
2. **Communiquer** : Informer les utilisateurs
3. **Isoler le problème** : Identifier la cause
4. **Résoudre** : Appliquer la solution
5. **Vérifier** : Tester la résolution
6. **Documenter** : Enregistrer l'incident

#### Restauration de sauvegarde
1. **Arrêter l'application**
2. **Sauvegarder l'état actuel**
3. **Restaurer la sauvegarde**
4. **Vérifier l'intégrité**
5. **Redémarrer l'application**
6. **Tester les fonctionnalités**

## 📞 Support utilisateur

### Niveaux de support

#### Niveau 1 - Support de base
- **Problèmes de connexion**
- **Questions d'utilisation**
- **Demandes de réinitialisation de mot de passe**

#### Niveau 2 - Support technique
- **Problèmes de permissions**
- **Erreurs d'application**
- **Demandes de modification de données**

#### Niveau 3 - Support administrateur
- **Problèmes de configuration**
- **Incidents système**
- **Demandes de développement**

### Outils de support

#### Console d'administration
- **Voir les logs** en temps réel
- **Tester les connexions** LDAP
- **Vérifier les permissions** utilisateur
- **Exécuter des commandes** système

#### Base de connaissances
- **FAQ** : Questions fréquentes
- **Procédures** : Guides étape par étape
- **Troubleshooting** : Résolution de problèmes

## 🔒 Sécurité et audit

### Journalisation des actions

#### Actions tracées
- **Connexions/déconnexions**
- **Création/modification d'utilisateurs**
- **Changements de permissions**
- **Création/modification d'agences/services**
- **Accès aux données sensibles**

#### Format des logs
```
[2024-01-15 10:30:15] [INFO] User admin@hff.mg created user john.doe@hff.mg
[2024-01-15 10:35:22] [WARNING] Multiple failed login attempts for user@hff.mg
[2024-01-15 10:40:18] [ERROR] Database connection failed
```

### Audit de sécurité

#### Vérifications régulières
- **Comptes inactifs** : Identifier les comptes non utilisés
- **Permissions excessives** : Détecter les permissions trop larges
- **Tentatives de connexion** : Surveiller les tentatives suspectes
- **Accès aux données** : Contrôler l'accès aux informations sensibles

#### Rapports d'audit
- **Rapport mensuel** : Résumé des activités
- **Rapport trimestriel** : Analyse des tendances
- **Rapport annuel** : Bilan complet

## 📋 Checklist administrateur

### Tâches quotidiennes
- [ ] Vérifier les logs d'erreur
- [ ] Contrôler les tentatives de connexion
- [ ] Vérifier l'espace disque
- [ ] Consulter les alertes système

### Tâches hebdomadaires
- [ ] Analyser les statistiques d'utilisation
- [ ] Vérifier les sauvegardes
- [ ] Contrôler les comptes inactifs
- [ ] Mettre à jour la documentation

### Tâches mensuelles
- [ ] Générer les rapports d'audit
- [ ] Optimiser la base de données
- [ ] Vérifier les permissions utilisateur
- [ ] Planifier les maintenances

### Tâches trimestrielles
- [ ] Révision de la politique de sécurité
- [ ] Formation des nouveaux administrateurs
- [ ] Mise à jour des procédures
- [ ] Évaluation des performances

---

*Dernière mise à jour : Janvier 2024*
*Version : 1.0*
*Rédigé par : Équipe de développement HFF*
