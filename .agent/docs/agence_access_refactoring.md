# Refactoring: Logique d'accès aux agences

## Résumé des modifications

Le code de vérification des agences autorisées a été centralisé dans un service réutilisable `AgenceAccessService` au lieu d'être dupliqué dans chaque repository.

## Changements effectués

### 1. **Nouveau service créé** : [`AgenceAccessService`](file:///d:/hff_symfony_5/src/Service/Security/AgenceAccessService.php)

Ce service gère toute la logique liée aux permissions d'accès aux agences :

```php
// Récupérer les IDs des agences autorisées
$agenceIds = $agenceAccessService->getAuthorizedAgenceIds($user);
// Retourne: null si admin/accès total, array d'IDs sinon

// Vérifier si l'utilisateur a accès à toutes les agences
$hasFullAccess = $agenceAccessService->hasAccessToAllAgences($user);

// Vérifier l'accès à une agence spécifique
$canAccess = $agenceAccessService->hasAccessToAgence($user, $agenceId);
```

**Logique implémentée** :
- ✅ Si l'utilisateur a le rôle `ROLE_ADMIN` → accès à toutes les agences (retourne `null`)
- ✅ Si un `UserAccess` a `allAgence = true` → accès à toutes les agences (retourne `null`)
- ✅ Sinon, collecte tous les IDs d'agences spécifiques depuis les `UserAccess`

### 2. **Repository simplifié** : [`DomRepository`](file:///d:/hff_symfony_5/src/Repository/Hf/Rh/Dom/DomRepository.php)

#### Méthode `filtredAgenceService` refactorisée

**Avant** :
```php
private function filtredAgenceService($queryBuilder, $options)
{
    if (!$options['boolean']) {
        $agenceIdAutoriser = is_array($options['idAgence']) 
            ? $options['idAgence'] 
            : [$options['idAgence']];
        $queryBuilder->andWhere('d.agenceEmetteurId IN (:agenceIdAutoriser)')
            ->setParameter('agenceIdAutoriser', $agenceIdAutoriser);
    }
}
```

**Après** :
```php
private function filtredAgenceService($queryBuilder, ?array $agenceIds): void
{
    // Si $agenceIds est null => accès à toutes les agences (admin)
    if ($agenceIds !== null && count($agenceIds) > 0) {
        $queryBuilder->andWhere('d.agenceEmetteurId IN (:agenceIdAutoriser)')
            ->setParameter('agenceIdAutoriser', $agenceIds);
    }
}
```

#### Méthode `findPaginatedAndFiltered` mise à jour

Nouveau paramètre optionnel `$agenceIds` :
```php
public function findPaginatedAndFiltered(
    int $page = 1, 
    int $limit = 10, 
    DomSearchDto $domSearchDto, 
    ?array $agenceIds = null
)
```

### 3. **Contrôleur modifié** : [`DomsListeController`](file:///d:/hff_symfony_5/src/Controller/Hf/Rh/Dom/Liste/DomsListeController.php)

**Avant** :
```php
public function index(Request $request, DomRepository $domRepository, ...)
{
    $paginationData = $domRepository->findPaginatedAndFiltered($page, $limit, $domSearchDto);
}
```

**Après** :
```php
public function index(
    Request $request, 
    DomRepository $domRepository,
    AgenceAccessService $agenceAccessService
) {
    // Récupérer les agences autorisées
    $agenceIdsAutorises = $agenceAccessService->getAuthorizedAgenceIds($this->getUser());
    
    // Passer au repository
    $paginationData = $domRepository->findPaginatedAndFiltered(
        $page, 
        $limit, 
        $domSearchDto, 
        $agenceIdsAutorises
    );
}
```

## Avantages de cette approche

✅ **Séparation des responsabilités** : Le repository ne contient que la logique SQL  
✅ **Réutilisabilité** : Le service peut être utilisé dans n'importe quel contrôleur  
✅ **Testabilité** : Plus facile de créer des tests unitaires pour la logique d'autorisation  
✅ **Maintenabilité** : Un seul endroit à modifier si la logique change  
✅ **Lisibilité** : Code plus clair et expressif

## Utilisation dans d'autres contrôleurs

Pour utiliser ce pattern ailleurs :

```php
use App\Service\Security\AgenceAccessService;

class MonController extends AbstractController
{
    public function maMethode(AgenceAccessService $agenceAccessService)
    {
        // Récupérer les agences autorisées
        $agenceIds = $agenceAccessService->getAuthorizedAgenceIds($this->getUser());
        
        // Utiliser dans une requête
        $results = $repository->findByAgences($agenceIds);
    }
}
```

## Compatibilité avec les Voters existants

Ce service s'intègre parfaitement avec vos [`ContextVoter`](file:///d:/hff_symfony_5/src/Security/Voter/ContextVoter.php) et [`PermissionVoter`](file:///d:/hff_symfony_5/src/Security/Voter/PermissionVoter.php) en extrayant une partie de leur logique pour la réutiliser dans les contextes de requêtes de base de données.
