# 🏗️ Architecture du Projet

## Vue d'ensemble

Le projet est construit sur **Symfony 5.4** et utilise une architecture MVC classique.
Le frontend est géré via **Webpack Encore**, utilisant **Bootstrap 5** et **jQuery** pour l'interactivité.

## 📂 Structure des Dossiers

Voici les dossiers clés pour comprendre l'organisation du code :

### `src/` (Backend)

-   **`Controller/`** : Contient la logique de routage et de réponse HTTP.
    -   `Home/` : Contrôle la page d'accueil et le tableau de bord principal.
    -   `Hf/` : Contient les contrôleurs spécifiques aux modules "Vignettes" (RH, Compta, etc.). C'est le dossier qui contient les contrôleurs spécifique pour chaque sociétés (ex: HF, etc.)
    -   `Admin/` : Gestion back-office.
-   **`Entity/`** : Classes PHP mappées à la base de données via Doctrine.
-   **`Repository/`** : Classes pour les requêtes SQL personnalisées (EntityRepository).
-   **`Security/`** : Gestionnaire d'authentification (LDAP, UserProvider, Voter).
-   **`Service/`** : Logique métier réutilisable (ex: Export Excel, Mailer).

### `templates/` (Frontend)

-   `base.html.twig` : Layout principal.
-   `partials/` : Fragments réutilisables (Navbar, Breadcrumb, Sidebar).
-   `hf/`, `home/`, `admin/` : Vues correspondant aux contrôleurs.

### `assets/` (Frontend Sources)

-   `app.js` / `app.css` : Points d'entrée principaux.
-   `controllers/` : Contrôleurs Stimulus (si utilisés).
-   `js/` : Scripts personnalisés.

## 🧩 Le Système de "Vignettes"

L'application est découpée en modules fonctionnels appelés "Vignettes" (visibles sur la page d'accueil).
Chaque vignette correspond généralement à un sous-dossier dans `src/Controller/Hf/` et `templates/hf/`.

Les 12 vignettes principales sont :
1.  **Documentation** (Annuaire, Procédures)
2.  **Reporting** (Power BI, Excel)
3.  **Compta** (Paiements, Bons de caisse)
4.  **RH** (Congés, Missions, Mutations)
5.  **Matériel** (Mouvements, Commandes)
6.  **Atelier** (Interventions, Planning)
7.  **Magasin** (Inventaire, Pièces)
8.  **Appro** (Achats, Commandes Fournisseurs)
9.  **IT** (Support Informatique)
10. **POL** (Pneu, Outil, Lubrifiant)
11. **Energie** (Jirama, Man)
12. **HSE** (Hygiène, Sécurité, Environnement)

### Routage

Les routes suivent la convention `/nom-vignette/nom-action`.
Exemple : `/rh/dom/liste` pour la liste des Départs Ordre de Mission.
