
# Documentation des Voters

Cette documentation décrit les voters utilisés dans l'application pour gérer les permissions.

## Introduction aux Voters

Les voters sont un mécanisme de sécurité de Symfony qui permet de gérer des logiques de permissions complexes. Chaque voter est une classe qui implémente `Symfony\Component\Security\Core\Authorization\Voter\Voter` et qui est chargée de voter sur une permission (un "attribut") pour un objet donné (un "sujet").

Le service de sécurité de Symfony (`security.authorization_checker`) consulte les voters chaque fois que la méthode `isGranted()` est appelée.

## Les Voters de l'application

L'application utilise les voters suivants :

1.  [PermissionVoter](#permissionvoter)
2.  [ContextVoter](#contextvoter)
3.  [VignetteVoter](/admin/documentation/voter/VignetteVoter.md)
4.  [ObjectVoter](#objectvoter)

---

### PermissionVoter

**Fichier :** `src/Security/Voter/PermissionVoter.php`

#### Rôle

Ce voter est le plus fondamental. Il vérifie si un utilisateur possède une **permission métier** donnée, représentée par un code (une chaîne de caractères).

Exemples de codes de permission : `RH_CONGE_CREATE`, `APPRO_BC_VALIDATE`, `ADMIN_USER_EDIT`.

#### Utilisation

On l'utilise pour vérifier des droits qui ne sont pas forcément liés à un objet spécifique.

```php
// Dans un contrôleur
if ($this->isGranted('RH_CONGE_CREATE')) {
    // L'utilisateur peut créer une demande de congé
}
```

#### Logique

1.  **Permissions directes :** Le voter vérifie d'abord si l'utilisateur a la permission directement associée à son profil.
2.  **Accès étendus (`UserAccess`) :** Il parcourt ensuite les "accès étendus" de l'utilisateur. Un `UserAccess` peut donner des permissions pour un périmètre fonctionnel (toutes les agences, une agence spécifique, etc.).
3.  **Rôle `ROLE_ADMIN` :** Si l'utilisateur a le rôle `ROLE_ADMIN`, il a tous les droits.

---

### ContextVoter

**Fichier :** `src/Security/Voter/ContextVoter.php`

#### Rôle

Ce voter vérifie si un utilisateur a le droit d'accéder à une ressource en fonction de son **contexte organisationnel** (agence et service).

#### Utilisation

Il est souvent utilisé en conjonction avec d'autres voters. Le sujet doit être un tableau contenant une instance d'`Agence` et une instance de `Service`.

```php
// Dans un contrôleur ou un autre voter
$agence = $demandeDeConge->getAgence();
$service = $demandeDeConge->getService();

if ($this->isGranted('CONTEXT_ACCESS', [$agence, $service])) {
    // L'utilisateur a le droit d'intervenir sur ce périmètre
}
```

#### Logique

1.  **Rôle `ROLE_ADMIN` :** Accès total.
2.  **Accès étendus (`UserAccess`) :** Le voter analyse les `UserAccess` de l'utilisateur pour voir s'il a une correspondance :
    *   Accès à toutes les agences et tous les services.
    *   Accès à toutes les agences pour un service donné.
    *   Accès à tous les services pour une agence donnée.
    *   Accès à une agence et un service spécifiques.

---

### VignetteVoter

**Fichier :** `src/Security/Voter/VignetteVoter.php`

#### Rôle

Ce voter détermine si un utilisateur a le droit de **voir ou d'accéder à une application** (représentée par une "vignette" sur la page d'accueil).

#### Utilisation

Il est utilisé pour afficher dynamiquement les vignettes sur la page d'accueil en fonction des droits de l'utilisateur.

```twig
{# Dans un template Twig #}
{% if is_granted('APPLICATION_ACCESS', vignette) %}
    <a href="{{ vignette.url }}">
        {{ vignette.nom }}
    </a>
{% endif %}
```

#### Logique

1.  **Rôle `ROLE_ADMIN` :** Accès total.
2.  **Permissions de l'utilisateur :** Le voter vérifie si l'utilisateur possède au moins une permission dont le code commence par le nom de la vignette.
    *   Par exemple, pour la vignette `RH`, l'utilisateur doit avoir une permission comme `RH_CONGE_VIEW`, `RH_SALAIRE_READ`, etc.
    *   Cette vérification est faite sur les permissions directes et les permissions issues des `UserAccess`.

---

### ObjectVoter

**Fichier :** `src/Security/Voter/ObjectVoter.php`

#### Rôle

Ce voter est un **voter générique** pour les entités (objets métier). Il combine une vérification de permission métier et une vérification de contexte.

Il gère les actions standards : `VIEW`, `EDIT`, `DELETE`, `VALIDATE`, `CREATE`.

#### Utilisation

On l'utilise pour sécuriser les actions CRUD sur les entités.

```php
// Dans un contrôleur
$demandeConge = ...; // Récupérer une instance de DemandeConge

// Vérifie si l'utilisateur peut voir cette demande
$this->denyAccessUnlessGranted('VIEW', $demandeConge);

// Vérifie s'il peut la modifier
if ($this->isGranted('EDIT', $demandeConge)) {
    // ...
}
```

#### Logique

1.  **Mapping Entité -> Préfixe de permission :** Le voter utilise un mapping pour déterminer le préfixe de permission associé à une classe d'entité.
    *   Exemple : `DemandeConge::class => 'RH_CONGE_'`.
2.  **Vérification de la permission métier :** Il vérifie si l'utilisateur a la permission requise en combinant le préfixe et l'action.
    *   Exemple : pour `isGranted('VIEW', $demandeConge)`, il vérifie la permission `RH_CONGE_VIEW` (en utilisant le `PermissionVoter`).
3.  **Vérification du contexte :** Si l'entité est liée à une agence ou un service, il vérifie que l'utilisateur a bien accès à ce contexte (en utilisant le `ContextVoter`).
4.  **Cas particulier `VIEW` :** Si l'action est `VIEW` et que l'utilisateur est le créateur de l'objet, il est toujours autorisé à le voir.

