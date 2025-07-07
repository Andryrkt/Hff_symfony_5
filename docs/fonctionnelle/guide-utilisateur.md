# 📘 Guide Utilisateur - HFFINTRANET

## 🎯 Introduction

### Qu'est-ce que HFFINTRANET ?
HFFINTRANET est une application web interne de gestion des demandes et des ressources pour l'entreprise HFF. Elle permet aux employés de soumettre des demandes, de suivre leur statut et de gérer les ressources selon leurs permissions.

### À qui s'adresse cette application ?
- **Tous les employés** : Pour soumettre des demandes
- **Managers** : Pour approuver les demandes de leur équipe
- **Administrateurs** : Pour gérer les utilisateurs, agences et services
- **Responsables RH** : Pour gérer les demandes RH
- **Responsables Appro** : Pour gérer les demandes d'approvisionnement

### Objectifs de l'application
- ✅ Dématérialiser les processus de demande
- ✅ Centraliser la gestion des demandes
- ✅ Améliorer le suivi et la traçabilité
- ✅ Optimiser les processus d'approbation
- ✅ Faciliter la communication entre services

## 🔐 Connexion à l'application

### Adresse d'accès
```
https://hffintranet.local
```

### Procédure de connexion
1. **Ouvrir votre navigateur** (Chrome, Firefox, Edge recommandés)
2. **Saisir l'adresse** de l'application
3. **Renseigner vos identifiants** :
   - **Nom d'utilisateur** : votre adresse email professionnelle
   - **Mot de passe** : votre mot de passe Windows/Active Directory
4. **Cliquer sur "Se connecter"**

### En cas de problème de connexion
- ✅ Vérifier que votre mot de passe est correct
- ✅ Vérifier votre connexion réseau
- ✅ Contacter le service informatique si le problème persiste

## 🏠 Tableau de bord

### Page d'accueil
Après connexion, vous accédez à votre tableau de bord personnel qui affiche :

#### 📊 Statistiques personnelles
- **Mes demandes en cours** : Nombre de demandes soumises
- **Demandes en attente** : Demandes en attente d'approbation
- **Demandes approuvées** : Demandes validées ce mois
- **Demandes rejetées** : Demandes refusées

#### 🚀 Actions rapides
- **Nouvelle demande** : Créer une demande
- **Mes demandes** : Voir toutes vos demandes
- **Demandes à approuver** : (si vous êtes manager)

#### 📢 Notifications
- Alertes sur les demandes en attente
- Notifications de nouvelles demandes à approuver
- Messages système importants

## 🧭 Navigation dans l'application

### Menu principal
```
🏠 Accueil
📝 Mes demandes
✅ Demandes à approuver (si manager)
👥 Gestion utilisateurs (si admin)
🏢 Gestion agences (si admin)
🔧 Gestion services (si admin)
📊 Statistiques
⚙️ Mon profil
🚪 Déconnexion
```

### Barre de recherche
- **Recherche globale** : Trouver rapidement des demandes
- **Filtres avancés** : Par date, statut, type, agence

## 📝 Fonctionnalités principales

### 🔧 Créer une nouvelle demande

#### Objectif
Soumettre une demande de matériel, formation, ou autre besoin.

#### Étapes détaillées
1. **Cliquer sur "Nouvelle demande"** dans le tableau de bord
2. **Remplir le formulaire** :
   - **Titre** : Description courte de la demande
   - **Description** : Détails de la demande
   - **Type** : Matériel, Formation, RH, Autre
   - **Priorité** : Basse, Normale, Haute, Urgente
   - **Agence** : Votre agence (pré-rempli)
   - **Service** : Votre service (pré-rempli)
3. **Joindre des documents** (optionnel) :
   - Cliquer sur "Ajouter un fichier"
   - Sélectionner le document
   - Formats acceptés : PDF, DOC, XLS, JPG, PNG
4. **Cliquer sur "Soumettre"**

#### 📸 Exemple de formulaire
```
Titre : Demande d'ordinateur portable
Description : Besoin d'un ordinateur portable pour les déplacements
Type : Matériel
Priorité : Normale
Agence : Agence Centrale
Service : Service Informatique
```

#### ✅ Conseils
- **Soyez précis** dans la description
- **Joignez des documents** si nécessaire
- **Vérifiez les informations** avant soumission

### 📋 Consulter mes demandes

#### Objectif
Suivre l'état de vos demandes soumises.

#### Étapes
1. **Cliquer sur "Mes demandes"** dans le menu
2. **Utiliser les filtres** :
   - Par statut (En attente, Approuvée, Rejetée)
   - Par date
   - Par type
3. **Cliquer sur une demande** pour voir les détails

#### Statuts des demandes
- 🟡 **En attente** : En cours de traitement
- 🟢 **Approuvée** : Validée par le manager
- 🔴 **Rejetée** : Refusée avec motif
- 🔵 **En cours** : En cours de réalisation
- ✅ **Terminée** : Demande finalisée

### ✅ Approuver des demandes (Managers)

#### Objectif
Valider ou refuser les demandes de votre équipe.

#### Étapes
1. **Cliquer sur "Demandes à approuver"**
2. **Consulter la liste** des demandes en attente
3. **Cliquer sur une demande** pour voir les détails
4. **Choisir l'action** :
   - **Approuver** : Valider la demande
   - **Rejeter** : Refuser avec motif
5. **Ajouter un commentaire** (optionnel)
6. **Confirmer l'action**

#### 📝 Exemple de commentaire d'approbation
```
Demande approuvée. Le matériel sera livré dans 2 semaines.
```

#### 📝 Exemple de commentaire de rejet
```
Demande rejetée : Budget insuffisant pour ce trimestre.
```

### 👥 Gestion des utilisateurs (Administrateurs)

#### Objectif
Gérer les comptes utilisateurs et leurs permissions.

#### Fonctionnalités
- **Créer un utilisateur** : Ajouter un nouvel employé
- **Modifier un utilisateur** : Changer les informations
- **Désactiver un utilisateur** : Retirer l'accès
- **Gérer les permissions** : Attribuer agences/services

#### Étapes pour créer un utilisateur
1. **Cliquer sur "Gestion utilisateurs"**
2. **Cliquer sur "Nouvel utilisateur"**
3. **Remplir le formulaire** :
   - Email professionnel
   - Rôles (Utilisateur, Manager, Admin)
   - Agences autorisées
   - Services autorisés
4. **Cliquer sur "Créer"**

### 🏢 Gestion des agences (Administrateurs)

#### Objectif
Gérer les agences de l'entreprise.

#### Fonctionnalités
- **Créer une agence** : Ajouter une nouvelle agence
- **Modifier une agence** : Changer les informations
- **Désactiver une agence** : Retirer de la liste active

#### Informations d'une agence
- Nom de l'agence
- Code unique
- Adresse
- Téléphone
- Email
- Services rattachés

### 🔧 Gestion des services (Administrateurs)

#### Objectif
Gérer les services de chaque agence.

#### Fonctionnalités
- **Créer un service** : Ajouter un nouveau service
- **Modifier un service** : Changer les informations
- **Rattacher à une agence** : Associer service et agence

## 📊 Statistiques et rapports

### Statistiques personnelles
- **Demandes par mois** : Évolution de vos demandes
- **Taux d'approbation** : Pourcentage de demandes approuvées
- **Types de demandes** : Répartition par catégorie

### Statistiques globales (Managers/Admins)
- **Demandes par agence** : Répartition géographique
- **Demandes par service** : Répartition par service
- **Temps de traitement** : Durée moyenne d'approbation

## ⚙️ Mon profil

### Modifier mes informations
1. **Cliquer sur "Mon profil"**
2. **Modifier les informations** :
   - Téléphone
   - Adresse
   - Photo de profil
3. **Cliquer sur "Enregistrer"**

### Changer mon mot de passe
- Le mot de passe est géré par l'Active Directory
- Contacter le service informatique pour un changement

## 🚨 Gestion des erreurs

### Problèmes courants

#### Erreur 403 - Accès interdit
**Cause** : Vous n'avez pas les permissions nécessaires
**Solution** : Contacter votre manager ou l'administrateur

#### Erreur 404 - Page non trouvée
**Cause** : Lien cassé ou page supprimée
**Solution** : Utiliser la navigation principale

#### Erreur de connexion
**Cause** : Identifiants incorrects ou compte désactivé
**Solution** : Vérifier vos identifiants ou contacter l'IT

#### Page qui ne se charge pas
**Cause** : Problème de connexion ou serveur indisponible
**Solution** : 
- Vérifier votre connexion internet
- Recharger la page (F5)
- Contacter le service informatique

## 📱 Utilisation mobile

### Compatibilité
- **Smartphones** : Interface responsive
- **Tablettes** : Optimisé pour écrans tactiles
- **Navigateurs** : Chrome, Safari, Firefox

### Fonctionnalités mobiles
- ✅ Créer des demandes
- ✅ Consulter le statut
- ✅ Approuver des demandes (managers)
- ✅ Recevoir des notifications

## 🔒 Sécurité et bonnes pratiques

### Règles de sécurité
- **Ne jamais partager** vos identifiants
- **Se déconnecter** après utilisation
- **Fermer le navigateur** sur ordinateurs partagés
- **Signaler** tout comportement suspect

### Protection des données
- **Données confidentielles** : Ne pas partager d'informations sensibles
- **Documents** : Vérifier le contenu avant upload
- **Sauvegarde** : L'application sauvegarde automatiquement

## 📞 Support et aide

### Aide contextuelle
- **Icône "?"** : Aide sur chaque page
- **Tooltips** : Informations au survol
- **Messages d'erreur** : Explications détaillées

### Contact support
- **Email** : support@hff.mg
- **Téléphone** : +261 20 123 456
- **Service** : Service Informatique
- **Horaires** : 8h-17h (Lun-Ven)

### FAQ
**Q : Comment récupérer mon mot de passe ?**
R : Contacter le service informatique

**Q : Puis-je modifier une demande soumise ?**
R : Non, une fois soumise, la demande ne peut plus être modifiée

**Q : Combien de temps pour une approbation ?**
R : Généralement 24-48h selon la priorité

**Q : Puis-je joindre plusieurs fichiers ?**
R : Oui, jusqu'à 5 fichiers de 10MB chacun

## 📋 Checklist utilisateur

### Première connexion
- [ ] Se connecter avec ses identifiants
- [ ] Vérifier ses informations de profil
- [ ] Consulter le tableau de bord
- [ ] Lire les notifications

### Utilisation quotidienne
- [ ] Consulter les nouvelles demandes
- [ ] Suivre l'état des demandes soumises
- [ ] Approuver les demandes (si manager)
- [ ] Vérifier les notifications

### Maintenance
- [ ] Mettre à jour son profil si nécessaire
- [ ] Signaler les problèmes rencontrés
- [ ] Participer aux formations utilisateur

---

*Dernière mise à jour : Janvier 2024*
*Version : 1.0*
