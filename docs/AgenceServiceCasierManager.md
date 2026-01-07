# AgenceServiceCasierManager - Guide d'utilisation

## 📋 Description

Le `AgenceServiceCasierManager` est un gestionnaire TypeScript qui permet de gérer des sélecteurs en cascade pour **Agence → Service → Casier**.

Contrairement à `AgenceServiceManager` qui gère seulement Agence → Service, ce gestionnaire ajoute un troisième niveau (Casier) avec chargement dynamique via API.

## 🎯 Fonctionnalités

- ✅ Chargement des **Agences** depuis les données pré-chargées
- ✅ Chargement des **Services** depuis les données pré-chargées (filtré par agence)
- ✅ Chargement des **Casiers** via API (filtré par agence)
- ✅ Support de plusieurs groupes de sélecteurs (emetteur, debiteur, destinataire)
- ✅ Gestion des valeurs pré-sélectionnées
- ✅ Gestion des erreurs de chargement

## 📦 Structure des données

### Données pré-chargées (Agences + Services)

```html
<div id="agence-service-data" data-agences='[
    {
        "id": 1,
        "code": "AG001",
        "nom": "Agence Principale",
        "services": [
            {"id": 1, "code": "SRV01", "nom": "Service Comptabilité"},
            {"id": 2, "code": "SRV02", "nom": "Service RH"}
        ]
    }
]'></div>
```

### API pour les Casiers

L'API doit retourner un tableau d'objets avec `value` et `text` :

```json
[
    {"value": "1", "text": "Casier A - Bureau 101"},
    {"value": "2", "text": "Casier B - Bureau 102"}
]
```

**Endpoint attendu** : `casier-fetch/{agenceId}`

## 🔧 Installation

### 1. Structure HTML requise

Vos sélecteurs doivent avoir les classes CSS suivantes :

```html
<!-- Pour l'émetteur -->
<select class="agenceEmetteur">...</select>
<select class="serviceEmetteur">...</select>
<select class="casierEmetteur">...</select>

<!-- Pour le débiteur -->
<select class="agenceDebiteur">...</select>
<select class="serviceDebiteur">...</select>
<select class="casierDebiteur">...</select>

<!-- Pour le destinataire -->
<select class="agenceDestinataire">...</select>
<select class="serviceDestinataire">...</select>
<select class="casierDestinataire">...</select>
```

### 2. Initialisation globale (dans app.ts)

```typescript
import { initAgenceServiceCasierHandlers } from "./js/utils/AgenceServiceCasierManager";

// Dans la méthode initManagers()
private initManagers(): void {
    // ... autres initialisations
    initAgenceServiceCasierHandlers();
}
```

### 3. Initialisation dans un contrôleur Stimulus

```typescript
import { Controller } from "@hotwired/stimulus";
import { initAgenceServiceCasierHandlers } from '@utils/AgenceServiceCasierManager';

export default class extends Controller {
    connect() {
        initAgenceServiceCasierHandlers();
    }
}
```

## 🎬 Comportement

### Scénario 1 : Sélection d'une agence

1. L'utilisateur sélectionne une **Agence**
2. Le select **Service** se remplit automatiquement avec les services de cette agence
3. Le select **Casier** se remplit automatiquement via un appel API

### Scénario 2 : Aucune agence sélectionnée

1. Les selects **Service** et **Casier** sont vidés
2. Les selects **Service** et **Casier** sont désactivés

### Scénario 3 : Valeurs pré-sélectionnées

1. Si les selects ont des valeurs pré-sélectionnées (formulaire d'édition)
2. Le gestionnaire restaure automatiquement les sélections après le chargement

## 🔍 Exemple complet

### Template Twig

```twig
{# Données pré-chargées #}
<div id="agence-service-data" 
     data-agences="{{ agences|json_encode|e('html_attr') }}"
     style="display: none;">
</div>

{# Formulaire #}
<div class="row">
    <div class="col-md-4">
        <label>Agence Émetteur</label>
        <select class="form-control agenceEmetteur" name="agence_emetteur">
            <option value="">-- Choisir une Agence --</option>
        </select>
    </div>
    
    <div class="col-md-4">
        <label>Service Émetteur</label>
        <select class="form-control serviceEmetteur" name="service_emetteur" disabled>
            <option value="">-- Choisir un Service --</option>
        </select>
    </div>
    
    <div class="col-md-4">
        <label>Casier Émetteur</label>
        <select class="form-control casierEmetteur" name="casier_emetteur" disabled>
            <option value="">-- Choisir un Casier --</option>
        </select>
    </div>
</div>
```

### Contrôleur Symfony (API Casier)

```php
/**
 * @Route("/casier-fetch/{agenceId}", name="casier_fetch", methods={"GET"})
 */
public function fetchCasiers(int $agenceId): JsonResponse
{
    $casiers = $this->casierRepository->findBy([
        'agenceRattacher' => $agenceId,
        'isValide' => true
    ]);

    $data = array_map(function (Casier $casier) {
        return [
            'value' => $casier->getId(),
            'text' => sprintf('%s - %s', $casier->getNumero(), $casier->getNom())
        ];
    }, $casiers);

    return new JsonResponse($data);
}
```

## 🆚 Différences avec AgenceServiceManager

| Fonctionnalité | AgenceServiceManager | AgenceServiceCasierManager |
|----------------|---------------------|---------------------------|
| Niveaux de cascade | 2 (Agence → Service) | 3 (Agence → Service → Casier) |
| Chargement Service | Données pré-chargées | Données pré-chargées |
| Chargement Casier | ❌ Non supporté | ✅ Via API |
| API requise | ❌ Non | ✅ Oui (`casier-fetch/{agenceId}`) |
| Utilisation | Formulaires simples | Formulaires avec gestion de casiers |

## 🐛 Débogage

### Vérifier les données chargées

Ouvrez la console du navigateur et vérifiez :

```javascript
// Les données d'agences sont chargées
console.log('Agences data:', agencesData);

// Les casiers sont chargés via API
// Vous verrez : "Casiers chargés: [...]"
```

### Erreurs courantes

1. **"Data container #agence-service-data not found"**
   - Vérifiez que l'élément `<div id="agence-service-data">` existe dans votre template

2. **"Failed to parse agences data"**
   - Vérifiez que l'attribut `data-agences` contient un JSON valide

3. **"Erreur lors du chargement des casiers"**
   - Vérifiez que l'endpoint API `casier-fetch/{agenceId}` existe et retourne le bon format

## 📝 Notes

- Les classes CSS doivent suivre le pattern : `.agence{Key}`, `.service{Key}`, `.casier{Key}`
- Les clés supportées par défaut : `emetteur`, `debiteur`, `destinataire`
- Le gestionnaire utilise `FetchManager` pour les appels API
- Les casiers sont rechargés à chaque changement d'agence
