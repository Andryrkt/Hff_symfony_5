# 🚀 Optimisations de Performance - Module DOM

## Vue d'ensemble

Le module DOM (Demande d'Ordre de Mission) a été optimisé pour améliorer significativement les temps de chargement et l'expérience utilisateur.

## Problèmes identifiés et résolus

### 1. **Requêtes N+1 dans le formulaire**
**Problème** : Le formulaire chargeait tous les personnels avec leurs relations, causant des requêtes multiples.

**Solution** :
- Optimisation des requêtes avec jointures
- Utilisation du cache de requêtes Doctrine
- Chargement en lot des entités

### 2. **Absence de mise en cache**
**Problème** : Les données statiques étaient rechargées à chaque requête.

**Solution** :
- Service de cache dédié (`DomCacheService`)
- Cache des catégories (30 minutes)
- Cache des sous-types de documents (1 heure)
- Cache des RMQ (1 heure)

### 3. **Requêtes multiples non optimisées**
**Problème** : Plusieurs requêtes séparées au lieu d'une seule requête optimisée.

**Solution** :
- Requêtes DQL optimisées avec jointures
- Utilisation de `DISTINCT` pour éviter les doublons
- Limitation des résultats avec `setMaxResults()`

### 4. **Performance côté client**
**Problème** : Appels AJAX multiples et absence de debouncing.

**Solution** :
- Cache côté client avec `Map`
- Debouncing de 300ms pour les appels AJAX
- Headers optimisés pour les requêtes

## Optimisations implémentées

### Backend (PHP/Symfony)

#### 1. Service de Cache DOM
```php
// src/Service/Dom/DomCacheService.php
class DomCacheService
{
    public function getMissionSousTypeDocument(): ?DomSousTypeDocument
    public function getCategoriesByCriteria(int $sousTypeDocId, string $rmqDescription): array
    public function getRmqByDescription(string $description): ?DomRmq
}
```

#### 2. Contrôleur optimisé
- Injection du service de cache
- Requêtes optimisées avec cache
- Gestion d'erreurs améliorée

#### 3. Formulaire optimisé
- Requêtes avec jointures pour éviter les N+1
- Cache des accès utilisateur
- Requêtes directes optimisées

#### 4. DTO optimisé
- Utilisation du service de cache
- Fallback vers requête directe si nécessaire

### Frontend (TypeScript/JavaScript)

#### 1. Cache côté client
```typescript
private categoryCache = new Map<string, any[]>();
```

#### 2. Debouncing
```typescript
private debounceTimer: number | null = null;
// Debounce de 300ms pour éviter les appels multiples
```

#### 3. Headers optimisés
```typescript
headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
}
```

## Configuration

### Cache Symfony
```yaml
# config/packages/dom_performance.yaml
parameters:
    dom.cache.categories_lifetime: 1800  # 30 minutes
    dom.cache.sous_types_lifetime: 3600  # 1 heure
    dom.cache.rmq_lifetime: 3600         # 1 heure
```

### Cache Doctrine
```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        query_cache_driver:
            type: pool
            pool: doctrine.system_cache_pool
        result_cache_driver:
            type: pool
            pool: doctrine.result_cache_pool
```

## Gains de performance attendus

### Backend
- **Temps de chargement initial** : 60-80% d'amélioration
- **Requêtes base de données** : 70-90% de réduction
- **Utilisation mémoire** : 30-50% de réduction
- **Cache hit ratio** : 85-95% pour les données statiques

### Frontend
- **Temps de réponse AJAX** : 50-70% d'amélioration
- **Appels réseau** : 80-90% de réduction (grâce au cache)
- **Expérience utilisateur** : Debouncing évite les clics multiples

## Script d'optimisation

Un script dédié a été créé pour optimiser le module DOM :

```bash
./scripts/optimize-dom.sh
```

Ce script :
1. Nettoie le cache Symfony
2. Réchauffe le cache
3. Optimise les requêtes Doctrine
4. Compile les assets frontend
5. Optimise Composer
6. Vérifie les permissions
7. Teste les routes DOM

## Monitoring et maintenance

### Métriques à surveiller
- Temps de réponse de la route `/dom/first`
- Nombre de requêtes base de données
- Taux de cache hit
- Temps de chargement des catégories

### Maintenance du cache
```bash
# Vider le cache DOM
php bin/console cache:pool:clear dom.cache_pool

# Vider tout le cache
php bin/console cache:clear
```

### Logs de performance
Les logs de performance sont disponibles dans :
- `var/log/prod.log` (production)
- `var/log/dev.log` (développement)

## Recommandations supplémentaires

### 1. **Base de données**
- Ajouter des index sur les colonnes fréquemment utilisées
- Utiliser Redis pour le cache en production
- Monitorer les requêtes lentes avec EXPLAIN

### 2. **Serveur web**
- Utiliser PHP-FPM avec OPcache
- Configurer un reverse proxy (Nginx/Apache)
- Utiliser HTTP/2 pour les assets

### 3. **CDN et assets**
- Utiliser un CDN pour les assets statiques
- Optimiser les images (WebP, compression)
- Minifier CSS/JS en production

## Dépannage

### Problèmes courants

#### 1. Cache non fonctionnel
```bash
# Vérifier la configuration du cache
php bin/console debug:config framework cache
```

#### 2. Requêtes lentes
```bash
# Activer le profiler Doctrine
# Vérifier dans le profiler web les requêtes
```

#### 3. Assets non compilés
```bash
# Recompiler les assets
npm run build
```

## Tests de performance

### Test de charge
```bash
# Utiliser Apache Bench pour tester
ab -n 100 -c 10 http://localhost/dom/first
```

### Test de mémoire
```bash
# Surveiller l'utilisation mémoire
php bin/console debug:container --show-arguments | grep memory
```

## Conclusion

Ces optimisations devraient considérablement améliorer les performances du module DOM. Le temps de chargement initial devrait passer de plusieurs secondes à moins d'une seconde, et l'expérience utilisateur sera beaucoup plus fluide.

Pour toute question ou problème, consulter les logs et utiliser les outils de debug Symfony.
