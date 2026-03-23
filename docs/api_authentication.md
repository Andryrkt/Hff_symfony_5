# Authentification API avec JWT

## Vue d'ensemble

L'API utilise JWT (JSON Web Tokens) pour l'authentification stateless. Les utilisateurs doivent d'abord s'authentifier via LDAP pour obtenir un token, puis utiliser ce token pour accéder aux endpoints protégés.

## Obtenir un token JWT

### Endpoint
```
POST /api/login
```

### Headers
```
Content-Type: application/json
```

### Body
```json
{
  "username": "votre_username",
  "password": "votre_mot_de_passe"
}
```

### Réponse en cas de succès
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### Exemples

#### Avec curl
```bash
curl -X POST https://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"votre_username","password":"votre_mot_de_passe"}'
```

#### Avec Postman
1. Créer une nouvelle requête POST
2. URL: `https://127.0.0.1:8000/api/login`
3. Headers: `Content-Type: application/json`
4. Body (raw JSON):
   ```json
   {
     "username": "votre_username",
     "password": "votre_mot_de_passe"
   }
   ```

## Utiliser le token JWT

Une fois le token obtenu, incluez-le dans l'en-tête `Authorization` de vos requêtes :

### Headers
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

### Exemple avec curl
```bash
curl -X GET https://127.0.0.1:8000/api/fetch-materiel \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
```

### Exemple avec Postman
1. Créer une nouvelle requête GET
2. URL: `https://127.0.0.1:8000/api/fetch-materiel`
3. Onglet "Authorization":
   - Type: Bearer Token
   - Token: `eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...`

## Gestion des erreurs

### 401 Unauthorized
Le token est invalide, expiré ou absent.

**Solution**: Obtenir un nouveau token via `/api/login`

### 403 Forbidden
L'utilisateur n'a pas les permissions nécessaires pour accéder à la ressource.

### Erreurs d'authentification
```json
{
  "error": "Invalid credentials."
}
```

## Durée de validité

Les tokens JWT expirent après **1 heure** par défaut. Après expiration, vous devez obtenir un nouveau token.

## Sécurité

- ⚠️ Ne partagez jamais votre token JWT
- ⚠️ Utilisez HTTPS en production
- ⚠️ Stockez les tokens de manière sécurisée (pas dans le localStorage pour les applications web)
- ⚠️ Les tokens ne peuvent pas être révoqués avant expiration

## Configuration

Les clés JWT sont stockées dans `config/jwt/`:
- `private.pem` : Clé privée pour signer les tokens
- `public.pem` : Clé publique pour valider les tokens

La passphrase est définie dans `.env` :
```
JWT_PASSPHRASE=9310ddd60d51a69e98ce4eda0bb7eac1b4494dc223cf7e4c9fe88694c30b4916
```
