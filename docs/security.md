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

Exemple de logique pour éditer une demande :
1.  Le Voter vérifie si l'utilisateur est l'auteur de la demande.
2.  OU si l'utilisateur possède un `UserAccess` correspondant à l'agence/service de la demande avec la permission `EDIT`.

### Permissions `UserAccess`

L'entité `UserAccess` (voir [Base de Données](database.md)) est liée à des `Permission`.
Exemples de permissions :
-   `CREATE`
-   `READ`
-   `UPDATE`
-   `DELETE`
-   `VALIDATE` (pour les workflows de validation)

## Bonnes Pratiques

-   Toujours utiliser `$this->isGranted('PERMISSION', $subject)` dans les contrôleurs.
-   Ne pas vérifier les ID en dur (ex: `if ($user->getId() == 1)`), utiliser les rôles ou voters.
