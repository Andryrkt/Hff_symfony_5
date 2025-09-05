# Guide : Comment récupérer l'utilisateur connecté dans Symfony

## 1. Méthodes principales

### Méthode 1 : Via le service Security (Recommandée)
```php
use Symfony\Component\Security\Core\Security;

public function monAction(Security $security)
{
    $user = $security->getUser();
    
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté.');
    }
    
    // Utiliser $user...
}
```

### Méthode 2 : Via AbstractController (Plus simple)
```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MonController extends AbstractController
{
    public function monAction()
    {
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }
        
        // Utiliser $user...
    }
}
```

### Méthode 3 : Via la session (Si vous stockez l'ID)
```php
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

public function monAction(SessionInterface $session, EntityManagerInterface $em)
{
    $userId = $session->get('user_id');
    
    if (!$userId) {
        throw $this->createAccessDeniedException('Vous devez être connecté.');
    }
    
    $user = $em->getRepository(User::class)->find($userId);
    
    if (!$user) {
        throw $this->createAccessDeniedException('Utilisateur non trouvé.');
    }
    
    // Utiliser $user...
}
```

## 2. Vérifications de sécurité

### Vérifier si l'utilisateur est connecté
```php
// Méthode 1
if (!$this->getUser()) {
    throw $this->createAccessDeniedException('Accès refusé');
}

// Méthode 2
$user = $this->getUser();
if (!$user) {
    return $this->redirectToRoute('login');
}
```

### Vérifier les rôles
```php
// Vérifier un rôle spécifique
if (!$this->isGranted('ROLE_ADMIN')) {
    throw $this->createAccessDeniedException('Accès refusé');
}

// Vérifier plusieurs rôles
if (!$this->isGranted(['ROLE_ADMIN', 'ROLE_USER'])) {
    throw $this->createAccessDeniedException('Accès refusé');
}
```

## 3. Exemples pratiques

### Dans un contrôleur complet
```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MonController extends AbstractController
{
    /**
     * @Route("/mon-action", name="mon_action")
     */
    public function monAction(Request $request, Security $security)
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        
        // Vérifier la connexion
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('login');
        }
        
        // Vérifier les rôles
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }
        
        // Utiliser l'utilisateur
        $nom = $user->getNom();
        $email = $user->getEmail();
        
        return $this->render('mon_template.html.twig', [
            'user' => $user,
            'nom' => $nom,
            'email' => $email,
        ]);
    }
}
```

### Dans un service
```php
<?php

namespace App\Service;

use Symfony\Component\Security\Core\Security;

class MonService
{
    private $security;
    
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function getCurrentUser()
    {
        return $this->security->getUser();
    }
    
    public function isUserConnected()
    {
        return $this->security->getUser() !== null;
    }
}
```

## 4. Configuration de sécurité

### Dans security.yaml
```yaml
security:
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
    
    firewalls:
        main:
            pattern: ^/
            form_login:
                login_path: login
                check_path: login
            logout:
                path: logout
                target: home
    
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/user, roles: ROLE_USER }
```

## 5. Bonnes pratiques

### ✅ À faire
- Toujours vérifier si l'utilisateur est connecté
- Utiliser les annotations de sécurité quand possible
- Gérer les cas d'erreur proprement
- Utiliser `$this->getUser()` dans les contrôleurs

### ❌ À éviter
- Ne pas vérifier la connexion
- Utiliser des appels incorrects comme `$sessionService()`
- Oublier de gérer les cas d'erreur
- Stocker des données sensibles en session

## 6. Dépannage

### Problème : `$this->getUser()` retourne null
**Solution :** Vérifiez que l'utilisateur est bien connecté et que la configuration de sécurité est correcte.

### Problème : Erreur "Service not found"
**Solution :** Injectez le service Security dans votre méthode ou utilisez `$this->getUser()`.

### Problème : L'utilisateur n'est pas trouvé
**Solution :** Vérifiez que l'entité User existe et que le provider est correctement configuré.

## 7. Exemple corrigé du DomFirstController

```php
public function firstForm(Request $request, SessionInterface $sessionService, EntityManagerInterface $entityManager, Security $security)
{
    // Récupérer l'utilisateur connecté
    $user = $security->getUser();
    
    // Vérifier la connexion
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
    }
    
    // Utiliser l'utilisateur...
    $agenceAutoriserId = $user->getAgenceAutoriserIds();
    // ... reste du code
}
```

Ce guide couvre toutes les méthodes principales pour récupérer l'utilisateur connecté dans Symfony de manière sécurisée et efficace.
