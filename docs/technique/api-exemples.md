# Exemples d'API REST pour le Projet HFF Symfony 5

Ce document présente les exemples d'API REST que vous pouvez créer avec ce projet de gestion d'agences et de services.

## Table des matières

1. [API Utilisateurs](#api-utilisateurs)
2. [API Agences](#api-agences)
3. [API Services](#api-services)
4. [API Authentification](#api-authentification)
5. [Exemples d'utilisation](#exemples-dutilisation)



## API Agences

### Endpoints disponibles

- `GET /api/agences` - Liste toutes les agences
- `GET /api/agences/{id}` - Récupère une agence par ID
- `POST /api/agences` - Crée une nouvelle agence
- `PUT /api/agences/{id}` - Met à jour une agence
- `DELETE /api/agences/{id}` - Supprime une agence
- `GET /api/agences/{id}/services` - Récupère les services d'une agence
- `GET /api/agences/{id}/users` - Récupère les utilisateurs d'une agence
- `GET /api/agences/search?q={query}` - Recherche d'agences

### Exemple de création d'agence

```bash
curl -X POST http://localhost:8000/api/agences \
  -H "Content-Type: application/json" \
  -d '{
    "code": "AG001",
    "nom": "Agence Paris Centre"
  }'
```

### Réponse attendue

```json
{
  "id": 1,
  "code": "AG001",
  "nom": "Agence Paris Centre",
  "createdAt": "2024-01-15T10:30:00+00:00",
  "updatedAt": "2024-01-15T10:30:00+00:00"
}
```

## API Services

### Endpoints disponibles

- `GET /api/services` - Liste tous les services
- `GET /api/services/{id}` - Récupère un service par ID
- `POST /api/services` - Crée un nouveau service
- `PUT /api/services/{id}` - Met à jour un service
- `DELETE /api/services/{id}` - Supprime un service
- `GET /api/services/{id}/agences` - Récupère les agences d'un service
- `GET /api/services/search?q={query}` - Recherche de services

### Exemple de création de service

```bash
curl -X POST http://localhost:8000/api/services \
  -H "Content-Type: application/json" \
  -d '{
    "code": "SVC001",
    "nom": "Service Informatique"
  }'
```

### Réponse attendue

```json
{
  "id": 1,
  "code": "SVC001",
  "nom": "Service Informatique",
  "createdAt": "2024-01-15T10:30:00+00:00",
  "updatedAt": "2024-01-15T10:30:00+00:00"
}
```



## Sécurité et Bonnes Pratiques

### 1. Authentification JWT

Pour une production, implémentez l'authentification JWT :

```bash
composer require lexik/jwt-authentication-bundle
```

### 2. Validation des données

Utilisez les contraintes de validation Symfony :

```php
use Symfony\Component\Validator\Constraints as Assert;

class UserApiRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 180)]
    public string $username;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public string $fullname;
}
```

### 3. Gestion des erreurs

```php
// Dans vos contrôleurs
try {
    // Logique métier
} catch (EntityNotFoundException $e) {
    return new JsonResponse(['error' => 'Ressource non trouvée'], 404);
} catch (ValidationException $e) {
    return new JsonResponse(['errors' => $e->getErrors()], 400);
} catch (Exception $e) {
    return new JsonResponse(['error' => 'Erreur interne'], 500);
}
```

### 4. Rate Limiting

```bash
composer require symfony/rate-limiter
```

### 5. Documentation OpenAPI

```bash
composer require nelmio/api-doc-bundle
```

## Tests des APIs

### Tests PHPUnit

```php
<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserApiControllerTest extends WebTestCase
{
    public function testGetUsers()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreateUser()
    {
        $client = static::createClient();
        $client->request('POST', '/api/users', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => 'test.user',
            'fullname' => 'Test User',
            'email' => 'test@example.com'
        ]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($client->getResponse()->getContent());
    }
}
```

### Tests avec Postman

1. Importez la collection Postman fournie
2. Configurez les variables d'environnement
3. Testez chaque endpoint
4. Vérifiez les réponses et codes de statut

## Conclusion

Ces exemples d'API vous donnent une base solide pour développer des applications frontend (web, mobile) qui interagissent avec votre système de gestion d'agences et de services. Les APIs sont conçues pour être RESTful, sécurisées et facilement extensibles selon vos besoins métier. 