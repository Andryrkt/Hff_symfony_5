# Migration de la Navigation HFF Intranet

## Vue d'ensemble

Ce document décrit la migration de la navigation depuis le projet `C:\wamp64\www\Hffintranet` vers le projet Symfony 5 actuel.

## Composants migrés

### 1. Templates Twig

#### Navigation principale (`templates/partials/_navbar.html.twig`)
- Barre de navigation avec logo HFF
- Chronomètre de session (15 minutes)
- Menu utilisateur avec dropdown administrateur
- Menu principal avec dropdowns
- Liens vers documentation et version

#### Sidebar (`templates/partials/_sidebar.html.twig`)
- Navigation latérale avec sections pliables
- Support pour l'application BDM
- Design responsive

#### Breadcrumb (`templates/partials/_breadcrumb.html.twig`)
- Fil d'Ariane avec icônes
- Support pour les liens et éléments actifs

#### Menu Item (`templates/partials/_menu_item.html.twig`)
- Macro réutilisable pour les éléments de menu
- Support des icônes et liens

### 2. Styles CSS

#### Navigation (`assets/styles/components/_navigation.scss`)
- Styles pour la barre de navigation
- Animations et transitions
- Design responsive
- Couleurs et thème HFF

#### Chronomètre (`assets/styles/components/_chronometer.scss`)
- Barre de progression animée
- Changement de couleur selon le temps restant
- Effets visuels (shimmer, pulse)

#### Sidebar (`assets/styles/components/_sidebar.scss`)
- Styles pour la navigation latérale
- Animations de collapse
- Hover effects

### 3. JavaScript/TypeScript

#### ChronometerManager (`assets/js/utils/chronometer.ts`)
- Gestion du chronomètre de session
- Mise à jour visuelle en temps réel
- Notifications d'expiration

#### SessionManager (`assets/js/utils/session.ts`)
- Gestion de la session utilisateur
- Détection d'activité
- Synchronisation entre onglets

#### ToastManager (`assets/js/utils/toast.ts`)
- Système de notifications toast
- Support de différents types (success, error, warning, info)
- Intégration Bootstrap

#### NavigationController (`assets/controllers/navigation_controller.ts`)
- Contrôleur Stimulus pour la navigation
- Gestion des dropdowns et sidebar
- Événements utilisateur

### 4. Contrôleurs PHP

#### HomeController (`src/Controller/HomeController.php`)
- Page d'accueil avec tableau de bord
- Données simulées pour le menu BDM
- Breadcrumb dynamique

## Fonctionnalités

### Chronomètre de session
- Compte à rebours de 15 minutes
- Barre de progression visuelle
- Changement de couleur (vert → orange → rouge)
- Déconnexion automatique
- Réinitialisation sur activité utilisateur

### Navigation responsive
- Menu burger sur mobile
- Sidebar pliable
- Dropdowns multi-colonnes pour l'administration

### Gestion des rôles
- Menu administrateur conditionnel
- Accès basé sur les rôles utilisateur
- Interface adaptative

### Notifications
- Système de toast intégré
- Avertissements de session
- Messages d'erreur et de succès

## Utilisation

### 1. Templates de base
```twig
{% extends 'base.html.twig' %}

{% block breadCrumb %}
    <div class="flex-grow-1">
        {% include 'partials/_breadcrumb.html.twig' %}
    </div>
{% endblock %}
```

### 2. Contrôleur avec menu
```php
public function index(): Response
{
    $mainMenu = [
        [
            'label' => 'Accueil',
            'route' => 'app_home',
            'icon' => 'fas fa-home',
            'visible' => true
        ],
        // ...
    ];

    return $this->render('template.html.twig', [
        'mainMenu' => $mainMenu
    ]);
}
```

### 3. Breadcrumb
```php
$breadcrumb = [
    ['label' => 'Accueil', 'url' => '/', 'icon' => 'fas fa-home'],
    ['label' => 'Section', 'icon' => 'fas fa-folder'],
    ['label' => 'Page actuelle', 'icon' => 'fas fa-file']
];
```

## Configuration

### Variables CSS
Les couleurs et styles sont définis dans `assets/styles/_variables.scss` :
- Couleur principale HFF : `#fbbb01`
- Police : `Noto Sans Nandinagari`
- Tailles de police adaptées

### JavaScript
Les utilitaires sont automatiquement initialisés dans `assets/app.ts` :
- ChronometerManager
- SessionManager  
- ToastManager

## Responsive Design

### Breakpoints
- Mobile : < 576px
- Tablet : 576px - 991px
- Desktop : > 991px

### Adaptations
- Chronomètre masqué sur mobile
- Sidebar en overlay sur tablette
- Menu burger sur petits écrans

## Améliorations apportées

1. **TypeScript** : Code plus robuste et maintenable
2. **Stimulus** : Contrôleurs réutilisables
3. **SCSS** : Styles modulaires et organisés
4. **Responsive** : Design adaptatif amélioré
5. **Accessibilité** : Support ARIA et navigation clavier

## Tests

Pour tester la navigation :
1. Démarrer le serveur : `php bin/console server:start`
2. Accéder à : `http://localhost:8000`
3. Tester les différents menus et fonctionnalités
4. Vérifier le chronomètre de session
5. Tester la responsivité

## Maintenance

### Ajout de nouveaux menus
1. Modifier le contrôleur pour ajouter l'élément au `$mainMenu`
2. Créer la route correspondante
3. Créer le template si nécessaire

### Modification des styles
1. Éditer les fichiers SCSS dans `assets/styles/components/`
2. Recompiler avec `npm run build`

### Ajout de fonctionnalités JavaScript
1. Créer un nouvel utilitaire dans `assets/js/utils/`
2. L'importer dans `assets/app.ts`
3. L'initialiser dans le DOMContentLoaded
