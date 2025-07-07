# 🔌 Documentation API - HFFINTRANET

## 🎯 Objectif
Ce document décrit les endpoints API disponibles dans l'application HFFINTRANET pour l'intégration avec d'autres systèmes.

## 🔐 Authentification

### Méthode d'authentification
L'API utilise l'authentification LDAP avec token JWT.

### Headers requis
```http
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

## 📋 Endpoints disponibles

### 🔐 Authentification

#### POST /api/login
Authentification utilisateur et récupération du token JWT.

**Request Body:**
```json
{
    "username": "user@company.com",
    "password": "user_password"
}
```

**Response (200):**
```json
{
    "success": true,
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "user": {
        "id": 1,
        "username": "user@company.com",
        "roles": ["ROLE_USER", "ROLE_ATELIER"],
        "permissions": {
            "agences": [1, 2, 3],
            "services": [1, 4, 7]
        }
    }
}
```

**Response (401):**
```json
{
    "success": false,
    "message": "Identifiants invalides"
}
```

### 👥 Gestion des utilisateurs

#### GET /api/users
Récupérer la liste des utilisateurs (avec pagination).

**Query Parameters:**
- `page` (int): Numéro de page (défaut: 1)
- `limit` (int): Nombre d'éléments par page (défaut: 20)
- `search` (string): Recherche par nom ou email
- `role` (string): Filtrer par rôle

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "username": "user@company.com",
            "email": "user@company.com",
            "roles": ["ROLE_USER"],
            "agences": [
                {
                    "id": 1,
                    "nom": "Agence Centrale",
                    "code": "AC"
                }
            ],
            "services": [
                {
                    "id": 1,
                    "nom": "Service Informatique",
                    "code": "SI"
                }
            ],
            "created_at": "2024-01-15T10:30:00+00:00",
            "updated_at": "2024-01-15T10:30:00+00:00"
        }
    ],
    "pagination": {
        "page": 1,
        "limit": 20,
        "total": 150,
        "pages": 8
    }
}
```

#### GET /api/users/{id}
Récupérer les détails d'un utilisateur spécifique.

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "username": "user@company.com",
        "email": "user@company.com",
        "roles": ["ROLE_USER", "ROLE_ATELIER"],
        "agences": [...],
        "services": [...],
        "groups": [
            {
                "id": 1,
                "nom": "Groupe Atelier",
                "code": "ATELIER"
            }
        ],
        "permissions": {
            "CREATE": true,
            "READ": true,
            "UPDATE": false,
            "DELETE": false
        }
    }
}
```

#### POST /api/users
Créer un nouvel utilisateur.

**Request Body:**
```json
{
    "username": "newuser@company.com",
    "email": "newuser@company.com",
    "roles": ["ROLE_USER"],
    "agence_ids": [1, 2],
    "service_ids": [1, 3],
    "group_ids": [1]
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Utilisateur créé avec succès",
    "data": {
        "id": 2,
        "username": "newuser@company.com"
    }
}
```

### 🏢 Gestion des agences

#### GET /api/agences
Récupérer la liste des agences.

**Query Parameters:**
- `page` (int): Numéro de page
- `limit` (int): Nombre d'éléments par page
- `search` (string): Recherche par nom ou code

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nom": "Agence Centrale",
            "code": "AC",
            "adresse": "123 Rue Principale",
            "telephone": "+261 20 123 456",
            "email": "ac@hff.mg",
            "services": [
                {
                    "id": 1,
                    "nom": "Service Informatique",
                    "code": "SI"
                }
            ],
            "created_at": "2024-01-15T10:30:00+00:00"
        }
    ]
}
```

#### GET /api/agences/{id}
Récupérer les détails d'une agence.

#### POST /api/agences
Créer une nouvelle agence.

**Request Body:**
```json
{
    "nom": "Nouvelle Agence",
    "code": "NA",
    "adresse": "456 Nouvelle Rue",
    "telephone": "+261 20 789 012",
    "email": "na@hff.mg"
}
```

### 🔧 Gestion des services

#### GET /api/services
Récupérer la liste des services.

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nom": "Service Informatique",
            "code": "SI",
            "description": "Service de gestion informatique",
            "agence_id": 1,
            "agence": {
                "id": 1,
                "nom": "Agence Centrale",
                "code": "AC"
            }
        }
    ]
}
```

### 📊 Gestion des demandes

#### GET /api/demandes
Récupérer la liste des demandes (selon les permissions de l'utilisateur).

**Query Parameters:**
- `status` (string): Statut de la demande (EN_ATTENTE, APPROUVEE, REJETEE)
- `type` (string): Type de demande
- `date_debut` (date): Date de début
- `date_fin` (date): Date de fin

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "titre": "Demande de matériel informatique",
            "description": "Besoin de nouveaux ordinateurs",
            "type": "MATERIEL",
            "status": "EN_ATTENTE",
            "demandeur": {
                "id": 1,
                "username": "user@company.com"
            },
            "agence": {
                "id": 1,
                "nom": "Agence Centrale"
            },
            "service": {
                "id": 1,
                "nom": "Service Informatique"
            },
            "created_at": "2024-01-15T10:30:00+00:00",
            "updated_at": "2024-01-15T10:30:00+00:00"
        }
    ]
}
```

#### POST /api/demandes
Créer une nouvelle demande.

**Request Body:**
```json
{
    "titre": "Nouvelle demande",
    "description": "Description de la demande",
    "type": "MATERIEL",
    "agence_id": 1,
    "service_id": 1,
    "priorite": "NORMALE"
}
```

#### PUT /api/demandes/{id}/status
Modifier le statut d'une demande.

**Request Body:**
```json
{
    "status": "APPROUVEE",
    "commentaire": "Demande approuvée"
}
```

## 📊 Statistiques

#### GET /api/stats/demandes
Récupérer les statistiques des demandes.

**Query Parameters:**
- `periode` (string): Période (JOUR, SEMAINE, MOIS, ANNEE)

**Response (200):**
```json
{
    "success": true,
    "data": {
        "total": 150,
        "en_attente": 45,
        "approuvees": 95,
        "rejetees": 10,
        "par_type": {
            "MATERIEL": 80,
            "FORMATION": 40,
            "AUTRE": 30
        },
        "par_agence": [
            {
                "agence": "Agence Centrale",
                "total": 50
            }
        ]
    }
}
```

## 🚨 Codes d'erreur

### Codes HTTP
- `200` - Succès
- `201` - Créé avec succès
- `400` - Requête invalide
- `401` - Non authentifié
- `403` - Non autorisé
- `404` - Ressource non trouvée
- `422` - Données invalides
- `500` - Erreur serveur

### Format des erreurs
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Les données fournies sont invalides",
        "details": {
            "username": ["Ce champ est requis"],
            "email": ["Format d'email invalide"]
        }
    }
}
```

## 📝 Exemples d'utilisation

### JavaScript (Fetch API)
```javascript
// Authentification
const login = async (username, password) => {
    const response = await fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, password })
    });
    
    const data = await response.json();
    if (data.success) {
        localStorage.setItem('token', data.token);
    }
    return data;
};

// Récupérer les utilisateurs
const getUsers = async (page = 1) => {
    const token = localStorage.getItem('token');
    const response = await fetch(`/api/users?page=${page}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
};
```

### PHP (cURL)
```php
// Authentification
function login($username, $password) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://hffintranet.local/api/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'username' => $username,
        'password' => $password
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Récupérer les utilisateurs
function getUsers($token, $page = 1) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://hffintranet.local/api/users?page={$page}");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
```

## 🔒 Sécurité

### Bonnes pratiques
1. **Toujours utiliser HTTPS** en production
2. **Valider les tokens** côté serveur
3. **Limiter les tentatives de connexion**
4. **Logger les accès API**
5. **Utiliser des rate limits**

### Rate Limiting
- **Authentification** : 5 tentatives par minute
- **Requêtes générales** : 100 requêtes par minute par utilisateur
- **Création de ressources** : 10 créations par minute par utilisateur

## 📞 Support API
Pour toute question sur l'API :
- Consulter les logs dans `var/log/api.log`
- Contacter l'équipe de développement
- Documentation technique : `/api/docs` (si disponible)
