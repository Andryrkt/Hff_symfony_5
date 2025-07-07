# 📚 Documentation HFFINTRANET

## 🎯 Vue d'ensemble

Bienvenue dans la documentation complète de l'application **HFFINTRANET**, une solution de gestion des demandes et des ressources pour l'entreprise HFF.

### Qu'est-ce que HFFINTRANET ?
HFFINTRANET est une application web interne développée avec Symfony 5 qui permet de :
- ✅ Dématérialiser les processus de demande
- ✅ Centraliser la gestion des demandes
- ✅ Améliorer le suivi et la traçabilité
- ✅ Optimiser les processus d'approbation
- ✅ Faciliter la communication entre services

## 📁 Structure de la documentation

```
docs/
├── 📖 README.md                    # Ce fichier - Vue d'ensemble
├── 📋 fonctionnelle/               # Documentation fonctionnelle
│   ├── 📘 guide-utilisateur.md     # Guide utilisateur complet
│   ├── 👨‍💼 manuel-administrateur.md # Manuel administrateur
│   └── 📋 procedures-metier.md     # Procédures métier
├── 🔧 technique/                   # Documentation technique
│   ├── 📦 installation.md          # Guide d'installation
│   ├── ⚙️ configuration.md         # Guide de configuration
│   └── 🔌 api.md                   # Documentation API
└── 🖼️ assets/                      # Ressources
    └── captures-ecran/             # Captures d'écran
```

## 👥 Public cible

### 📘 Documentation fonctionnelle
- **Utilisateurs finaux** : Employés utilisant l'application
- **Managers** : Responsables d'équipe
- **Administrateurs** : Gestionnaires de l'application
- **Responsables métier** : Chefs de service et directeurs

### 🔧 Documentation technique
- **Développeurs** : Équipe de développement
- **Administrateurs système** : Gestionnaires infrastructure
- **Intégrateurs** : Équipes d'intégration
- **Support technique** : Équipe de support

## 🚀 Démarrage rapide

### Pour les utilisateurs
1. **Lire le [Guide Utilisateur](fonctionnelle/guide-utilisateur.md)**
2. **Consulter les [Procédures Métier](fonctionnelle/procedures-metier.md)**
3. **Contacter le support** en cas de question

### Pour les administrateurs
1. **Suivre le [Guide d'Installation](technique/installation.md)**
2. **Configurer l'application** avec le [Guide de Configuration](technique/configuration.md)
3. **Consulter le [Manuel Administrateur](fonctionnelle/manuel-administrateur.md)**

### Pour les développeurs
1. **Installer l'environnement** de développement
2. **Consulter la [Documentation API](technique/api.md)**
3. **Contribuer** au développement

## 📋 Fonctionnalités principales

### 🔐 Authentification
- **Authentification LDAP** : Intégration avec l'Active Directory
- **Gestion des rôles** : Permissions granulaires
- **Sécurité renforcée** : Protection contre les attaques

### 📝 Gestion des demandes
- **Création de demandes** : Interface intuitive
- **Workflow d'approbation** : Processus configurable
- **Suivi en temps réel** : Statut des demandes
- **Notifications** : Alertes automatiques

### 👥 Gestion des utilisateurs
- **Profils utilisateur** : Informations détaillées
- **Permissions granulaires** : Par agence et service
- **Groupes d'appartenance** : Atelier, Magasin, RH, Appro

### 🏢 Gestion organisationnelle
- **Agences** : Structure géographique
- **Services** : Organisation par métier
- **Hiérarchie** : Relations agence/service

### 📊 Reporting et statistiques
- **Tableaux de bord** : Métriques en temps réel
- **Rapports personnalisés** : Export PDF/Excel
- **Analyses** : Tendances et performances

## 🔧 Architecture technique

### Stack technologique
- **Backend** : Symfony 5.4 (PHP 7.4+)
- **Frontend** : Twig, Bootstrap, JavaScript
- **Base de données** : SQL Server 2019 / Informix
- **Authentification** : LDAP
- **Assets** : Webpack Encore

### Composants principaux
- **Contrôleurs** : Gestion des requêtes HTTP
- **Entités** : Modèle de données Doctrine
- **Services** : Logique métier
- **Formulaires** : Validation des données
- **Templates** : Interface utilisateur

## 📞 Support et contact

### Équipe de développement
- **Email** : dev@hff.mg
- **Téléphone** : +261 20 123 456
- **Service** : Service Informatique

### Support utilisateur
- **Email** : support@hff.mg
- **Téléphone** : +261 20 123 456
- **Horaires** : 8h-17h (Lun-Ven)

### Documentation
- **Mise à jour** : Janvier 2024
- **Version** : 1.0
- **Responsable** : Équipe de développement HFF

## 🔄 Mise à jour de la documentation

### Processus de mise à jour
1. **Identifier le besoin** : Nouvelle fonctionnalité ou correction
2. **Rédiger la documentation** : Mise à jour des fichiers concernés
3. **Révision** : Validation par l'équipe
4. **Publication** : Mise en ligne de la nouvelle version
5. **Communication** : Information aux utilisateurs

### Versioning
- **Version majeure** : Changements importants (ex: 2.0)
- **Version mineure** : Nouvelles fonctionnalités (ex: 1.1)
- **Version patch** : Corrections et améliorations (ex: 1.0.1)

## 📚 Ressources supplémentaires

### Liens utiles
- **Application** : https://hffintranet.local
- **GitHub** : https://github.com/Andryrkt/Hff_symfony_5
- **Documentation Symfony** : https://symfony.com/doc/5.4/

### Outils de développement
- **Symfony CLI** : https://symfony.com/download
- **Composer** : https://getcomposer.org/
- **Node.js** : https://nodejs.org/

## 🤝 Contribution

### Comment contribuer
1. **Forker le projet** sur GitHub
2. **Créer une branche** pour votre fonctionnalité
3. **Développer** en suivant les standards
4. **Tester** vos modifications
5. **Soumettre une pull request**

### Standards de code
- **PSR-12** : Standards de codage PHP
- **Symfony Best Practices** : Bonnes pratiques Symfony
- **Documentation** : Commentaires et documentation

## 📄 Licence

Ce projet est propriétaire de HFF. Tous droits réservés.

---

*Dernière mise à jour : Janvier 2024*
*Version : 1.0*
*Rédigé par : Équipe de développement HFF* 