# Documentation du Projet HFF Intranet

## Résumé
Ce projet est une application web intranet pour HFF, construite avec le framework Symfony. Elle gère des entités administratives comme les agences, les services, les utilisateurs et leurs accès. L'application expose à la fois une interface web traditionnelle via Twig et une API JSON via API Platform.

## Technologies Principales
*   **Backend**: PHP 7.4+ avec **Symfony 5.4**
*   **Base de données**: **Doctrine ORM** pour la gestion des entités.
*   **API**: **API Platform** pour l'exposition des données.
*   **Frontend**:
    *   **Twig**: Moteur de templates pour le rendu des pages HTML.
    *   **Webpack Encore**: Pour la gestion des assets (CSS, JavaScript).
    *   **Bootstrap 5**, **jQuery**, **Select2**, **FontAwesome**: Bibliothèques frontend.
*   **Sécurité**: **Symfony Security** avec une authentification possible via **LDAP**.

## Structure et Modèles de Données
Le code est organisé autour des modèles de données (Entités) suivants :

#### Gestion Agences/Services:
*   `Agence`: Représente une agence.
*   `Service`: Représente un service.
*   `AgenceServiceIrium`: Fait le lien entre agences et services.

### Liste Complète des Agences

| Code | Nom |
| :--- | :--- |
| 01 | ANTANANARIVO |
| 02 | CESSNA IVATO |
| 20 | FORT-DAUPHIN |
| 30 | AMBATOVY |
| 40 | TAMATAVE |
| 50 | RENTAL |
| 60 | PNEU - OUTIL - LUB |
| 80 | ADMINISTRATION |
| 90 | COMM ENERGIE |
| 91 | ENERGIE DURABLE |
| 92 | ENERGIE JIRAMA |
| C1 | TRAVEL AIRWAYS |

### Liste Complète des Services

| Code | Nom |
| :--- | :--- |
| NEG | MAGASIN |
| COM | COMMERCIAL |
| ATE | ATELIER |
| CSP | CUSTOMER SUPPORT |
| GAR | GARANTIE |
| FOR | FORMATION |
| ASS | ASSURANCE |
| MAN | ENERGIE MAN |
| LCD | LOCATION |
| DIR | DIRECTION GENERALE |
| FIN | FINANCE |
| PER | PERSONNEL ET SECURITE |
| INF | INFORMATIQUE |
| IMM | IMMOBILIER |
| TRA | TRANSIT |
| APP | APPRO |
| UMP | UNITE METHODE ET PROCEDURES |
| ENG | ENGINEERIE ET REALISATIONS |
| VAN | VANILLE |
| GIR | GIROFLE |
| THO | THOMSON |
| TSI | TSIAZOMPANIRY |
| LTV | LOCATION TAMATAVE |
| LFD | LOCATION FORT DAUPHINE |
| LBV | LOCATION MORAMANGA |
| MAH | MAHAJANGA |
| NOS | NOSY BE |
| TUL | TOLIARA |
| AMB | AMBOHIMANAMBOLA |
| FLE | FLEXIBLE |
| TSD | TSIROANOMANDIDY |
| VAT | VATOMANDRY |
| BLK | BELOBABA |
| MAS | MOBILE ASSETS |
| MAP | MARCHE PUBLIC |
| ADM | ADMINISTRATION |
| LEV | LEVAGE DM |
| LR6 | LOCATION RN6 |
| LST | LOCATION STAR |
| LCJ | LOCATION CENTRALE JIRAMA |
| SLR | SOLAIRE |
| LGR | LOCATION GROUPES |
| LSC | LOCATION SAMCRETTE |
| C1 | TRAVEL AIRWAYS |

#### Gestion Utilisateurs/Personnel:
*   `User`: L'utilisateur qui se connecte.
*   `Personnel`: Représente un membre du personnel.
*   `UserAccess`: Gère les droits d'accès d'un utilisateur à une agence/service.

#### Gestion des Accès et Groupes:
*   `Group`: Un groupe d'utilisateurs.
*   `Application`: Représente une application ou un module.
*   `GroupAccess`: Gère les droits d'un groupe sur une application.

#### Statuts:
*   `StatutDemande`: Pour gérer différents statuts de demandes.

## Activité Récente

#### 5 Derniers Commits
- effacement pour bien débuté
- migration de DOm et navigation
- reorganesation des fichiers
- amelioration de la performance
- feat: Add comprehensive .gitignore and optimization scripts

#### Actions de Débogage Récentes (notre session)
1.  **Suppression d'une fonctionnalité obsolète**: Une entité nommée `DemandeOrdreMission` n'existait plus mais était encore référencée dans de nombreux fichiers (`Agence`, `Service`, `User`, `Personnel`, `StatutDemande`). Nous avons entièrement supprimé ces références pour corriger des erreurs de mapping Doctrine qui empêchaient l'application de fonctionner.
2.  **Correction des templates**: Nous avons corrigé des chemins de templates incorrects qui causaient des erreurs "template not found" pour la page d'accueil (`home/index.html.twig`) et la barre de navigation (`partials/_navbar.html.twig`).
