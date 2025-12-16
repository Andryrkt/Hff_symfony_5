# 🔐 Sécurité & Authentification

## Authentification (LDAP)

L'application utilise le composant Security de Symfony connecté à un annuaire **Active Directory (LDAP)**.

### Configuration (`security.yaml`)

L'authentification se fait via un `LdapUserProvider`.
Lorsqu'un utilisateur se connecte :
1.  Symfony vérifie ses identifiants contre le serveur LDAP.
2.  Si valide, l'utilisateur est connecté et ses rôles sont chargés.
3.  Si l'utilisateur n'existe pas en base de données locale (`User`), il peut être créé à la volée ou refusé selon la configuration.

## Autorisation (Voters & Rôles)

### Rôles Symfony

Les rôles sont hiérarchiques :
-   `ROLE_USER` : Utilisateur de base.
-   `ROLE_ADMIN` : Accès au back-office.
-   `ROLE_SUPER_ADMIN` : Accès complet.

### Voters (Permissions fines)

Pour gérer les droits complexes (a-t-il le droit de *voir* ce document spécifique ?), nous utilisons des **Voters** Symfony.
Ils se trouvent dans `src/Security/Voter/`.
> [Voir la documentation détaillée des Voters](Voter/Voters.md)

Exemple de logique pour voir une demande :
1.  Le Voter vérifie si l'utilisateur est l'auteur de la demande.
2.  OU si l'utilisateur possède un `UserAccess` correspondant à l'agence/service, et le type de document de la demande avec la permission `RH_ORDRE_MISSION_VIEWS` par exemple.

### Permissions `UserAccess`

L'entité `UserAccess` (voir [Base de Données](../Architecture/database.md)) est liée à des `Permission`.
Exemples de permissions :
-   `RH_ORDRE_MISSION_CREATE`(pour les workflows de validation)
-   `RH_ORDRE_MISSION_VIEWS`

-   Toujours utiliser `$this->isGranted('PERMISSION', $subject)` dans les contrôleurs.
-   Ne pas vérifier les ID en dur (ex: `if ($user->getId() == 1)`), utiliser les rôles ou voters.

## 🛡️ Filtrage Dynamique des Données (Contextual Security)

Au-delà des simples permissions "Oui/Non" (Voters), l'application filtre les données retournées par la base de données en fonction du périmètre de l'utilisateur.

### 1. Le Service de Contexte (`ContextAccessService`)

Le fichier `src/Service/Security/ContextAccessService.php` est le cerveau de ce système.
Il analyse les `UserAccess` de l'utilisateur pour déterminer sa portée de vue pour un module donné (ex: DOM).

*   **Entrée** : Utilisateur + Type de Document (ex: 'DOM').
*   **Sortie** : Une configuration de filtres (ex: "Accès à l'agence Tana uniquement, mais tous les services").
*   **Logique** :
    *   Si `ROLE_ADMIN` : Accès total.
    *   Sinon, il cumule les droits définis dans les entités `UserAccess` (droits globaux + droits spécifiques au module).

### 2. Le Trait de Filtrage (`DynamicContextFilterTrait`)

Le fichier `src/Repository/Traits/DynamicContextFilterTrait.php` est utilisé dans les Repository Doctrine.
Il injecte automatiquement des clauses `WHERE` SQL basées sur le contexte calculé ci-dessus.

**Exemple d'utilisation :**
L'utilisateur "Chef d'Agence Tana" consulte la liste des missions.
1.  `ContextAccessService` répond : `agenceIds = [1] (Tana)`, `allServices = true`.
2.  `DynamicContextFilterTrait` modifie la requête SQL :
    ```sql
    AND (d.agenceDebiteurId IN (1) OR d.agenceEmetteurId IN (1))
    ```
3.  L'utilisateur ne voit que les missions concernant Tana.

### 🔭 Permissions Directes

Il est possible d'attribuer une permission directe à un utilisateur , ceci permet à l'utilisateur d'avoir toujour l'accès à un module.
Cela permet de donner un droit exceptionnel (ex: `RH_MISSION_VALIDATE`) à une personne spécifique sur un périmètre précis, sans lui donner le rôle `ADMIN` complet.
