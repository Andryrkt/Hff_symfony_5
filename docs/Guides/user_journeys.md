# 🗺️ Parcours Utilisateur (User Journeys)

Cette page décrit les flux de navigation principaux de l'application.

## 🧭 Navigation Globale

1.  **Authentification (LDAP)**
    *   L'utilisateur accède à l'application.
    *   Si non connecté, il est redirigé vers la mire de login LDAP.
    *   Après succès, il arrive sur la **Page d'Accueil**.

2.  **Page d'Accueil (Dashboard)**
    *   L'utilisateur voit les **Vignettes** (cartes) correspondant aux modules auxquels il a accès (RH, Compta, etc.).
    *   Le clic sur une vignette le redirige vers le tableau de bord spécifique de ce module.

3.  **Navigation dans un Module**
    *   **Sidebar (Menu Latéral)** : Permet de naviguer entre les fonctionnalités du module (ex: Créer une demande, Voir la liste, Validation).
    *   **Breadcrumb (Fil d'Ariane)** : Permet de se repérer et de remonter rapidement dans l'arborescence (ex: `Accueil > RH > Missions > Création`).

## 🛫 Workflow : Demande d'Ordre de Mission (DOM)

Le processus de création d'une mission est découpé en plusieurs étapes pour simplifier la saisie.

### Étape 1 : Initialisation (`/dom-first-form`)
*   **Objectif** : Cadrer la demande.
*   **Actions** :
    *   Choix du **Type de Mission** (ex: Mission Ordinaire, Formation...).
    *   Choix de la **Catégorie** (ex: Technique, Administratif...).
*   **Technique** : À la validation, ces informations basiques sont stockées temporairement en **Session** (`dom_first_form_data`) pour être passées à l'étape suivante.

### Étape 2 : Saisie Détaillée (`/dom-second-form`)
*   **Objectif** : Remplir toutes les informations logistiques.
*   **Actions** :
    *   Récupération automatique du Demandeur (utilisateur connecté).
    *   Saisie des **Agences/Services Débiteurs** (qui paie ?).
    *   Saisie des **Dates et Lieux** de départ/arrivée.
    *   Choix du **Motif** et autres détails.
*   **Validation** :
    *   Vérification complète des données.
    *   Création de l'entité `Dom` en base de données.
    *   Génération automatique du PDF de la mission.
    *   Enregistrement dans l'historique (`HistoriqueOperationService`).

### Étape 3 : Suivi (`/dom/liste`)
*   Après validation, l'utilisateur est redirigé vers la **Liste des Demandes**.
*   Il peut voir son numéro de DOM, le statut de validation, et télécharger le PDF.
