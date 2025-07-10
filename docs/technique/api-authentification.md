## API Authentification

### Endpoints disponibles

- `POST /api/auth/login` - Authentification utilisateur
- `POST /api/auth/logout` - Déconnexion
- `GET /api/auth/me` - Informations de l'utilisateur connecté
- `GET /api/auth/permissions` - Permissions de l'utilisateur
- `POST /api/auth/check-access` - Vérification d'accès

### Exemple d'authentification

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "john.doe",
    "password": "motdepasse123"
  }' -k
```

### Réponse attendue

```json
{
  "message": "Authentification réussie",
  "user": {
    "id": 1,
    "username": "john.doe",
    "fullname": "John Doe",
    "email": "john.doe@example.com",
    "roles": ["ROLE_USER"]
  },
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Vérification d'accès

```bash
curl -X POST http://localhost:8000/api/auth/check-access \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "agence_code": "AG001",
    "service_code": "SVC001"
  }'
```

### Réponse attendue

```json
{
  "has_access": true,
  "user_id": 1,
  "agence_code": "AG001",
  "service_code": "SVC001"
}
```

## Exemples d'utilisation

### Application JavaScript/TypeScript

```javascript
// Classe pour gérer les appels API
class HffApiClient {
    constructor(baseUrl = 'http://localhost:8000/api') {
        this.baseUrl = baseUrl;
        this.token = localStorage.getItem('auth_token');
    }

    async login(username, password) {
        const response = await fetch(`${this.baseUrl}/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password })
        });
        
        const data = await response.json();
        if (data.token) {
            this.token = data.token;
            localStorage.setItem('auth_token', data.token);
        }
        
        return data;
    }

    async getUsers() {
        const response = await fetch(`${this.baseUrl}/users`, {
            headers: {
                'Authorization': `Bearer ${this.token}`
            }
        });
        return response.json();
    }

    async createUser(userData) {
        const response = await fetch(`${this.baseUrl}/users`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.token}`
            },
            body: JSON.stringify(userData)
        });
        return response.json();
    }

    async getAgences() {
        const response = await fetch(`${this.baseUrl}/agences`, {
            headers: {
                'Authorization': `Bearer ${this.token}`
            }
        });
        return response.json();
    }

    async checkAccess(agenceCode, serviceCode) {
        const response = await fetch(`${this.baseUrl}/auth/check-access`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.token}`
            },
            body: JSON.stringify({
                agence_code: agenceCode,
                service_code: serviceCode
            })
        });
        return response.json();
    }
}

// Utilisation
const api = new HffApiClient();

// Authentification
api.login('john.doe', 'password123')
    .then(data => console.log('Connecté:', data))
    .catch(error => console.error('Erreur:', error));

// Récupération des utilisateurs
api.getUsers()
    .then(users => console.log('Utilisateurs:', users))
    .catch(error => console.error('Erreur:', error));
```

### Application Mobile (React Native)

```javascript
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

class ApiService {
    constructor() {
        this.api = axios.create({
            baseURL: API_BASE_URL,
            timeout: 10000,
        });

        // Intercepteur pour ajouter le token d'authentification
        this.api.interceptors.request.use(
            (config) => {
                const token = AsyncStorage.getItem('auth_token');
                if (token) {
                    config.headers.Authorization = `Bearer ${token}`;
                }
                return config;
            },
            (error) => Promise.reject(error)
        );
    }

    async login(username, password) {
        try {
            const response = await this.api.post('/auth/login', {
                username,
                password
            });
            
            if (response.data.token) {
                await AsyncStorage.setItem('auth_token', response.data.token);
            }
            
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async getAgences() {
        try {
            const response = await this.api.get('/agences');
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async getServices() {
        try {
            const response = await this.api.get('/services');
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async getUserPermissions() {
        try {
            const response = await this.api.get('/auth/permissions');
            return response.data;
        } catch (error) {
            throw error;
        }
    }
}

export default new ApiService();
```