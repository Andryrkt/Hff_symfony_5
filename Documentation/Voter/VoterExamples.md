# Voters - Exemples et Tests

Ce document fournit des exemples concrets d'utilisation des voters, des cas d'usage réels du projet, et des exemples de tests unitaires.

## Table des matières

- [Exemples d'utilisation combinée](#exemples-dutilisation-combinée)
- [Cas d'usage réels](#cas-dusage-réels)
- [Tests unitaires](#tests-unitaires)
- [Fixtures pour les tests](#fixtures-pour-les-tests)

---

## Exemples d'utilisation combinée

### Exemple 1 : Sécurisation complète d'une action CRUD

```php
use App\Entity\DemandeConge;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CongeController extends AbstractController
{
    /**
     * Affichage de la liste des demandes de congé
     * Combine PermissionVoter + ContextVoter (via filtrage)
     */
    public function index(DemandeCongeRepository $repo)
    {
        // Vérifier la permission de base
        $this->denyAccessUnlessGranted('RH_CONGE_VIEW');
        
        // Récupérer toutes les demandes
        $demandes = $repo->findAll();
        
        // Filtrer selon le contexte de l'utilisateur
        $demandesAccessibles = array_filter($demandes, function($demande) {
            return $this->isGranted('CONTEXT_ACCESS', [
                $demande->getAgence(),
                $demande->getService()
            ]);
        });
        
        return $this->render('conge/index.html.twig', [
            'demandes' => $demandesAccessibles
        ]);
    }
    
    /**
     * Affichage d'une demande
     * Utilise ObjectVoter qui combine automatiquement Permission + Context
     */
    public function show(DemandeConge $demande)
    {
        // ObjectVoter vérifie :
        // 1. Permission RH_CONGE_VIEW (via PermissionVoter)
        // 2. Contexte agence/service (via ContextVoter)
        // 3. Si user = créateur, accès automatique
        $this->denyAccessUnlessGranted('VIEW', $demande);
        
        return $this->render('conge/show.html.twig', [
            'demande' => $demande
        ]);
    }
    
    /**
     * Création d'une demande
     */
    public function create(Request $request)
    {
        // Vérifier la permission de création
        $this->denyAccessUnlessGranted('RH_CONGE_CREATE');
        
        $demande = new DemandeConge();
        $form = $this->createForm(DemandeCongeType::class, $demande);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier le contexte avant de sauvegarder
            $this->denyAccessUnlessGranted('CONTEXT_ACCESS', [
                $demande->getAgence(),
                $demande->getService()
            ]);
            
            $demande->setCreatedBy($this->getUser());
            $this->entityManager->persist($demande);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('conge_show', ['id' => $demande->getId()]);
        }
        
        return $this->render('conge/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Modification d'une demande
     */
    public function edit(DemandeConge $demande, Request $request)
    {
        // ObjectVoter vérifie permission + contexte
        $this->denyAccessUnlessGranted('EDIT', $demande);
        
        $form = $this->createForm(DemandeCongeType::class, $demande);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Demande modifiée avec succès');
            return $this->redirectToRoute('conge_show', ['id' => $demande->getId()]);
        }
        
        return $this->render('conge/edit.html.twig', [
            'demande' => $demande,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Validation d'une demande (action sensible)
     */
    public function validate(DemandeConge $demande)
    {
        // Vérifier la permission de validation
        $this->denyAccessUnlessGranted('VALIDATE', $demande);
        
        // Vérifier que la demande n'est pas déjà validée
        if ($demande->isValidated()) {
            throw $this->createAccessDeniedException('Cette demande est déjà validée');
        }
        
        $demande->setValidated(true);
        $demande->setValidatedBy($this->getUser());
        $demande->setValidatedAt(new \DateTime());
        
        $this->entityManager->flush();
        
        $this->addFlash('success', 'Demande validée avec succès');
        return $this->redirectToRoute('conge_show', ['id' => $demande->getId()]);
    }
}
```

### Exemple 2 : Menu dynamique avec VignetteVoter

```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}Application{% endblock %}</title>
</head>
<body>
    <nav class="main-navigation">
        <div class="logo">Mon Application</div>
        
        <ul class="menu">
            {% for vignette in app_vignettes %}
                {% if is_granted('APPLICATION_ACCESS', vignette) %}
                    <li class="menu-item">
                        <a href="{{ vignette.url }}" class="menu-link">
                            <i class="{{ vignette.icon }}"></i>
                            <span>{{ vignette.nom }}</span>
                        </a>
                    </li>
                {% endif %}
            {% endfor %}
        </ul>
        
        <div class="user-menu">
            <span>{{ app.user.email }}</span>
            <a href="{{ path('app_logout') }}">Déconnexion</a>
        </div>
    </nav>
    
    <main>
        {% block body %}{% endblock %}
    </main>
</body>
</html>
```

### Exemple 3 : API REST avec vérifications

```php
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiCongeController extends AbstractController
{
    /**
     * @Route("/api/conges", methods={"GET"})
     */
    public function list(DemandeCongeRepository $repo): JsonResponse
    {
        $this->denyAccessUnlessGranted('RH_CONGE_VIEW');
        
        $demandes = $repo->findAll();
        
        // Filtrer selon le contexte
        $demandesAccessibles = array_filter($demandes, function($demande) {
            return $this->isGranted('VIEW', $demande);
        });
        
        return $this->json(array_map(function($demande) {
            return [
                'id' => $demande->getId(),
                'dateDebut' => $demande->getDateDebut()->format('Y-m-d'),
                'dateFin' => $demande->getDateFin()->format('Y-m-d'),
                'statut' => $demande->getStatut(),
                'canEdit' => $this->isGranted('EDIT', $demande),
                'canValidate' => $this->isGranted('VALIDATE', $demande),
                'canDelete' => $this->isGranted('DELETE', $demande),
            ];
        }, $demandesAccessibles));
    }
    
    /**
     * @Route("/api/conges/{id}", methods={"PUT"})
     */
    public function update(DemandeConge $demande, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('EDIT', $demande);
        
        $data = json_decode($request->getContent(), true);
        
        // Mettre à jour les champs...
        $demande->setDateDebut(new \DateTime($data['dateDebut']));
        $demande->setDateFin(new \DateTime($data['dateFin']));
        
        $this->entityManager->flush();
        
        return $this->json(['success' => true]);
    }
}
```

---

## Cas d'usage réels

### Cas 1 : Gestion multi-agences

**Contexte :** Une entreprise avec plusieurs agences. Les utilisateurs ne doivent voir que les données de leur(s) agence(s).

```php
// Configuration UserAccess pour un directeur d'agence
$agenceParis = $agenceRepo->findOneBy(['code' => 'PARIS']);

$userAccess = new UserAccess();
$userAccess->setAgence($agenceParis);
$userAccess->setAllService(true);  // Tous les services de Paris
$userAccess->addPermission($permissionRepo->findOneBy(['code' => 'RH_CONGE_VIEW']));
$userAccess->addPermission($permissionRepo->findOneBy(['code' => 'RH_CONGE_VALIDATE']));

$user->addUserAccess($userAccess);
```

```php
// Dans le contrôleur
public function dashboard()
{
    $this->denyAccessUnlessGranted('RH_CONGE_VIEW');
    
    // Récupérer toutes les demandes
    $demandes = $this->demandeRepo->findAll();
    
    // Filtrer automatiquement selon le contexte
    $demandesVisibles = array_filter($demandes, function($demande) {
        return $this->isGranted('VIEW', $demande);
    });
    
    // L'utilisateur ne voit que les demandes de l'agence de Paris
    return $this->render('dashboard.html.twig', [
        'demandes' => $demandesVisibles
    ]);
}
```

### Cas 2 : Responsable RH national

**Contexte :** Un responsable RH qui doit avoir accès au service RH de toutes les agences.

```php
$serviceRH = $serviceRepo->findOneBy(['code' => 'RH']);

$userAccess = new UserAccess();
$userAccess->setAllAgence(true);  // Toutes les agences
$userAccess->setService($serviceRH);  // Mais uniquement RH
$userAccess->addPermission($permissionRepo->findOneBy(['code' => 'RH_CONGE_VIEW']));
$userAccess->addPermission($permissionRepo->findOneBy(['code' => 'RH_CONGE_VALIDATE']));
$userAccess->addPermission($permissionRepo->findOneBy(['code' => 'RH_PERSONNEL_VIEW']));

$user->addUserAccess($userAccess);
```

### Cas 3 : Utilisateur avec accès à plusieurs modules

**Contexte :** Un utilisateur qui travaille à la fois en RH et en Comptabilité.

```php
// Accès RH pour l'agence de Lyon
$agenceLyon = $agenceRepo->findOneBy(['code' => 'LYON']);
$serviceRH = $serviceRepo->findOneBy(['code' => 'RH']);

$accessRH = new UserAccess();
$accessRH->setAgence($agenceLyon);
$accessRH->setService($serviceRH);
$accessRH->addPermission($permissionRepo->findOneBy(['code' => 'RH_CONGE_VIEW']));

// Accès Compta pour l'agence de Lyon
$serviceCompta = $serviceRepo->findOneBy(['code' => 'COMPTA']);

$accessCompta = new UserAccess();
$accessCompta->setAgence($agenceLyon);
$accessCompta->setService($serviceCompta);
$accessCompta->addPermission($permissionRepo->findOneBy(['code' => 'COMPTA_FACTURE_VIEW']));

$user->addUserAccess($accessRH);
$user->addUserAccess($accessCompta);
```

```twig
{# L'utilisateur verra 2 vignettes : RH et COMPTA #}
{% for vignette in vignettes %}
    {% if is_granted('APPLICATION_ACCESS', vignette) %}
        <div class="vignette">{{ vignette.nom }}</div>
    {% endif %}
{% endfor %}
```

---

## Tests unitaires

### Test du PermissionVoter

```php
namespace App\Tests\Security\Voter;

use App\Security\Voter\PermissionVoter;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\ApplicationGroupe\Permission;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PermissionVoterTest extends TestCase
{
    private PermissionVoter $voter;
    
    protected function setUp(): void
    {
        $this->voter = new PermissionVoter();
    }
    
    public function testSupportsAttribute()
    {
        $this->assertTrue($this->voter->supports('RH_CONGE_CREATE', null));
        $this->assertTrue($this->voter->supports('ANY_STRING', null));
    }
    
    public function testUserWithDirectPermissionIsGranted()
    {
        $user = new User();
        $permission = new Permission();
        $permission->setCode('RH_CONGE_CREATE');
        $user->addPermissionDirecte($permission);
        
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        
        $result = $this->voter->vote($token, null, ['RH_CONGE_CREATE']);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }
    
    public function testUserWithoutPermissionIsDenied()
    {
        $user = new User();
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        
        $result = $this->voter->vote($token, null, ['RH_CONGE_CREATE']);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }
    
    public function testAdminHasAllPermissions()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        
        $result = $this->voter->vote($token, null, ['ANY_PERMISSION']);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }
}
```

### Test du ContextVoter

```php
namespace App\Tests\Security\Voter;

use App\Security\Voter\ContextVoter;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ContextVoterTest extends TestCase
{
    private ContextVoter $voter;
    
    protected function setUp(): void
    {
        $this->voter = new ContextVoter();
    }
    
    public function testSupportsContextAccessAttribute()
    {
        $agence = new Agence();
        $service = new Service();
        
        $this->assertTrue($this->voter->supports('CONTEXT_ACCESS', [$agence, $service]));
        $this->assertFalse($this->voter->supports('OTHER_ATTRIBUTE', [$agence, $service]));
    }
    
    public function testUserWithAllAccessIsGranted()
    {
        $user = new User();
        $access = new UserAccess();
        $access->setAllAgence(true);
        $access->setAllService(true);
        $user->addUserAccess($access);
        
        $agence = new Agence();
        $service = new Service();
        
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $result = $this->voter->vote($token, [$agence, $service], ['CONTEXT_ACCESS']);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }
    
    public function testUserWithSpecificAgenceServiceAccess()
    {
        $agenceParis = $this->createAgence(1, 'Paris');
        $serviceRH = $this->createService(1, 'RH');
        
        $user = new User();
        $access = new UserAccess();
        $access->setAgence($agenceParis);
        $access->setService($serviceRH);
        $user->addUserAccess($access);
        
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        
        // Devrait avoir accès à Paris/RH
        $result = $this->voter->vote($token, [$agenceParis, $serviceRH], ['CONTEXT_ACCESS']);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
        
        // Ne devrait PAS avoir accès à Lyon/RH
        $agenceLyon = $this->createAgence(2, 'Lyon');
        $result = $this->voter->vote($token, [$agenceLyon, $serviceRH], ['CONTEXT_ACCESS']);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }
    
    private function createAgence(int $id, string $nom): Agence
    {
        $agence = new Agence();
        $reflection = new \ReflectionClass($agence);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($agence, $id);
        $agence->setNom($nom);
        return $agence;
    }
    
    private function createService(int $id, string $nom): Service
    {
        $service = new Service();
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($service, $id);
        $service->setNom($nom);
        return $service;
    }
}
```

### Test du VignetteVoter

```php
namespace App\Tests\Security\Voter;

use App\Security\Voter\VignetteVoter;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\ApplicationGroupe\Vignette;
use App\Entity\Admin\ApplicationGroupe\Permission;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class VignetteVoterTest extends TestCase
{
    private VignetteVoter $voter;
    
    protected function setUp(): void
    {
        $this->voter = new VignetteVoter();
    }
    
    public function testUserWithMatchingPermissionHasAccess()
    {
        $user = new User();
        $permission = new Permission();
        $permission->setCode('RH_CONGE_VIEW');
        $user->addPermissionDirecte($permission);
        
        $vignette = new Vignette();
        $vignette->setNom('RH');
        
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $result = $this->voter->vote($token, $vignette, ['APPLICATION_ACCESS']);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }
    
    public function testUserWithoutMatchingPermissionHasNoAccess()
    {
        $user = new User();
        $permission = new Permission();
        $permission->setCode('APPRO_BC_VIEW');  // Permission APPRO
        $user->addPermissionDirecte($permission);
        
        $vignette = new Vignette();
        $vignette->setNom('RH');  // Vignette RH
        
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $result = $this->voter->vote($token, $vignette, ['APPLICATION_ACCESS']);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }
}
```

---

## Fixtures pour les tests

### Fixture pour les permissions

```php
namespace App\DataFixtures;

use App\Entity\Admin\ApplicationGroupe\Permission;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PermissionFixtures extends Fixture
{
    public const PERMISSIONS = [
        // RH
        'RH_CONGE_VIEW',
        'RH_CONGE_CREATE',
        'RH_CONGE_EDIT',
        'RH_CONGE_DELETE',
        'RH_CONGE_VALIDATE',
        'RH_PERSONNEL_VIEW',
        'RH_SALAIRE_VIEW',
        
        // APPRO
        'APPRO_BC_VIEW',
        'APPRO_BC_CREATE',
        'APPRO_BC_VALIDATE',
        'APPRO_FOURNISSEUR_VIEW',
        
        // COMPTA
        'COMPTA_FACTURE_VIEW',
        'COMPTA_PAIEMENT_CREATE',
        
        // ADMIN
        'ADMIN_USER_EDIT',
        'ADMIN_PERMISSION_MANAGE',
    ];
    
    public function load(ObjectManager $manager): void
    {
        foreach (self::PERMISSIONS as $code) {
            $permission = new Permission();
            $permission->setCode($code);
            $permission->setLibelle($this->generateLibelle($code));
            
            $manager->persist($permission);
            $this->addReference('permission_' . $code, $permission);
        }
        
        $manager->flush();
    }
    
    private function generateLibelle(string $code): string
    {
        return str_replace('_', ' ', ucfirst(strtolower($code)));
    }
}
```

### Fixture pour les utilisateurs de test

```php
namespace App\DataFixtures;

use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\UserAccess;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('$2y$13$hashed_password');
        $manager->persist($admin);
        
        // Responsable RH Paris
        $rhParis = new User();
        $rhParis->setEmail('rh.paris@example.com');
        $rhParis->addPermissionDirecte($this->getReference('permission_RH_CONGE_VIEW'));
        $rhParis->addPermissionDirecte($this->getReference('permission_RH_CONGE_VALIDATE'));
        
        $accessRHParis = new UserAccess();
        $accessRHParis->setAgence($this->getReference('agence_paris'));
        $accessRHParis->setService($this->getReference('service_rh'));
        $rhParis->addUserAccess($accessRHParis);
        
        $manager->persist($rhParis);
        
        // Utilisateur simple
        $user = new User();
        $user->setEmail('user@example.com');
        $user->addPermissionDirecte($this->getReference('permission_RH_CONGE_VIEW'));
        $manager->persist($user);
        
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            PermissionFixtures::class,
            AgenceFixtures::class,
            ServiceFixtures::class,
        ];
    }
}
```

---

## Ressources

- [Voters.md - Documentation générale](Voters.md)
- [ContextVoter.md - Documentation détaillée](ContextVoter.md)
- [VignetteVoter.md - Documentation détaillée](VignetteVoter.md)
- [Documentation Symfony sur les Tests](https://symfony.com/doc/current/testing.html)
