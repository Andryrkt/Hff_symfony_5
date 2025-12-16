# 🏗️ Architecture du Projet

## Vue d'ensemble

Le projet est construit sur **Symfony 5.4** et utilise une architecture MVC classique.
Le frontend est géré via **Webpack Encore**, utilisant **Bootstrap 5** et **jQuery** pour l'interactivité.

## 📂 Structure des Dossiers

Voici les dossiers clés pour comprendre l'organisation du code :

### `src/` (Backend)

-   **`Controller/`** : Contient la logique de routage et de réponse HTTP.
    -   `Home/` : Contrôle la page d'accueil et le tableau de bord principal.
    -   `Hf/` : Contient les contrôleurs spécifiques à la société **Henri Fraise** (HF). L'architecture est prévue pour être multi-sociétés : d'autres dossiers pourront être ajoutés ici pour d'autres entités à l'avenir.
    -   `Admin/` : Gestion back-office.
-   **`Entity/`** : Classes PHP mappées à la base de données via Doctrine.
-   **`Repository/`** : Classes pour les requêtes SQL personnalisées (EntityRepository).
-   **`Security/`** : Gestionnaire d'authentification (LDAP, UserProvider, Voter).
-   **`Service/`** : Logique métier réutilisable (ex: Export Excel, Mailer).

### `templates/` (Frontend)

-   `base.html.twig` : Layout principal.
-   `partials/` : Fragments réutilisables (Navbar, Breadcrumb, Sidebar).
-   `macros/` : Fonctions Twig réutilisables (ex: affichage récursif, formulaires complexes).
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

## 🛠️ Comment ajouter une nouvelle Vignette ?

Pour ajouter un nouveau module (ex: "Transport") :

1.  **Créer le Contrôleur**
    Créer un dossier `src/Controller/Hf/Transport/` et un contrôleur `TransportController.php`.
    ```php
    /**
     * @Route("/transport")
     */
    class TransportController extends AbstractController { ... }
    ```

2.  **Créer les Vues**
    Créer un dossier `templates/hf/transport/` pour vos fichiers Twig.

3.  **Ajouter la Vignette sur l'accueil**
    Modifier `src/Service/HomeCardService.php` (ou le fichier de configuration équivalent) pour inclure votre vignette dans la liste des modules affichés, avec son icône et son lien.

4.  **Gérer les Droits**
    Si nécessaire, créer un `Voter` spécifique ou ajouter des règles dans `UserAccess` pour contrôler qui peut voir cette vignette.

## 📏 Nomenclature & Conventions

Pour maintenir la cohérence du code, merci de respecter ces conventions :

### Base de Données (Spécifique)
Contrairement aux conventions Symfony standard (snake_case avec `_id`), ce projet utilise souvent le **camelCase** pour les colonnes de clés étrangères.
-   **Standard** : `user_id`, `type_document_id`
-   **Projet HFF** : `userId`, `typeDocumentId`, `sousTypeDocumentId`

*Il est donc souvent nécessaire de spécifier manuellement le nom de la colonne dans les annotations Doctrine :*
```php
@ORM\JoinColumn(name="sousTypeDocumentId", referencedColumnName="id")
```

### Rutage & Contrôleurs
-   **Routes** : snake_case, préfixées par le module. Ex: `/rh/mission/liste`.
-   **Noms de route** : snake_case. Ex: `app_rh_mission_list`.
-   **Contrôleurs** : PascalCase, suffixé par `Controller`. Ex: `MissionController`.

### Variables & Code
-   **PHP** : Respect des standards PSR-12 (camelCase pour variables/méthodes, PascalCase pour classes).
-   **Twig** : snake_case pour les noms de fichiers (ex: `liste_mission.html.twig`).

