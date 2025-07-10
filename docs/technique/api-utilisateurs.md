## API Utilisateurs

### Endpoints disponibles

- `GET /api/users` - Liste tous les utilisateurs
- `GET /api/users/{id}` - Récupère un utilisateur par ID
- `POST /api/users` - Crée un nouvel utilisateur
- `PUT /api/users/{id}` - Met à jour un utilisateur
- `DELETE /api/users/{id}` - Supprime un utilisateur
- `GET /api/users/search?q={query}` - Recherche d'utilisateurs

### Exemple de création d'utilisateur

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "username": "john.doe",
    "fullname": "John Doe",
    "email": "john.doe@example.com",
    "matricule": "EMP001",
    "numero_telephone": "+33123456789",
    "poste": "Développeur",
    "roles": ["ROLE_USER", "ROLE_ADMIN"]
  }'
```

### Réponse attendue

```json
{
  "id": 1,
  "username": "john.doe",
  "fullname": "John Doe",
  "email": "john.doe@example.com",
  "matricule": "EMP001",
  "numero_telephone": "+33123456789",
  "poste": "Développeur",
  "roles": ["ROLE_USER", "ROLE_ADMIN"],
  "createdAt": "2024-01-15T10:30:00+00:00",
  "updatedAt": "2024-01-15T10:30:00+00:00"
}
```