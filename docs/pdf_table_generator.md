# Documentation : PdfTableGeneratorFlexible

Ce service est un moteur de rendu de tableaux HTML conçu spécifiquement pour l'export PDF via **TCPDF** dans Symfony. Il permet de transformer des tableaux de données (objets ou tableaux PHP) en structures HTML robustes et stylisées.

## 📥 Installation & Injection

Le service est automatiquement disponible via l'autowiring de Symfony.

```php
use App\Service\Utils\Fichier\PdfTableGeneratorFlexible;

public function __construct(PdfTableGeneratorFlexible $tableGenerator) {
    $this->tableGenerator = $tableGenerator;
}
```

---

## ⚙️ Configuration Globale (`setOptions`)

La méthode `setOptions(array $options)` permet de définir le comportement par défaut pour tout le tableau.

| Option | Type | Description | Valeur par défaut |
| :--- | :--- | :--- | :--- |
| `table_attributes` | `string` | Attributs HTML de la balise `<table>` | `border="0" ... style="font-size: 8px;"` |
| `header_row_style` | `string` | Style CSS pour la ligne d'en-tête (`<thead><tr>`) | `background-color: #D3D3D3;` |
| `footer_row_style` | `string` | Style CSS pour la ligne de pied de page (`<tfoot><tr>`) | `background-color: #D3D3D3;` |
| `empty_message` | `string` | Message affiché si aucune donnée n'est fournie | "Aucune donnée disponible" |
| `row_styler` | `callable` | Fonction `fn($row)` retournant un style CSS pour la ligne entière | `null` |
| `default_date_format` | `string` | Format PHP pour le rendu des colonnes de type `date` | `'d/m/Y'` |
| `default_empty_value` | `string` | Valeur de remplacement pour les champs `null` ou vides | `''` |

---

## 📊 Configuration des Colonnes (`headerConfig`)

Le cœur du générateur repose sur un tableau de configuration où chaque entrée définit une colonne.

### Propriétés de base
- **`key`** : Le chemin vers la donnée. Supporte la notation "point" pour les objets imbriqués (ex: `client.nom`) ou les index de tableaux (ex: `[0]`).
- **`label`** : Le titre de la colonne.
- **`width`** : Largeur en pixels (crucial pour le rendu TCPDF).

### Types & Formatage Automatique
Le champ **`type`** active des formateurs prédéfinis :
- **`text`** (par défaut) : Rendu brut.
- **`number`** : Formate comme un montant (2 décimales, séparateur de milliers).
- **`date`** / **`datetime`** : Gère les objets `DateTime` ou les chaînes de dates.
- **`boolean`** : Rendu "Oui" / "Non" (personnalisable via `true_label` / `false_label`).
- **`percent`** : Formate la valeur et ajoute le suffixe `%`.

### Personnalisation Avancée
- **`formatter`** : `callable function($value, $row)` permettant de transformer la valeur manuellement.
- **`styler`** : `callable function($value, $row)` retournant du CSS spécifique à la cellule selon la valeur (ex: mettre en rouge si négatif).
- **`style`** : CSS de base appliqué à la fois au `th` et au `td`.
- **`cell_style`** : Surcharge de style uniquement pour les cellules du corps (`td`).
- **`header_style`** : Surcharge de style uniquement pour l'en-tête (`th`).
- **`footer_style`** : Surcharge de style uniquement pour le pied de page.
- **`footer_colspan`** : (Nombre) Permet de fusionner plusieurs cellules dans le pied de page (ex: 2). Les colonnes suivantes fusionnées seront automatiquement ignorées dans le rendu du footer.

---

## 🚀 Exemple de Pied de page avec fusion (Colspan)

Si vous voulez afficher "TOTAL" sur les deux premières colonnes :

```php
$headerConfig = [
    [
        'key'   => 'ref',
        'label' => 'Référence',
        'width' => 50,
        'footer_colspan' => 2 // Fusionne cette colonne avec la suivante dans le footer
    ],
    [
        'key'   => 'designation',
        'label' => 'Désignation',
        'width' => 150
        // Pas besoin de config footer ici, elle est englobée par la précédente
    ],
    [
        'key'   => 'montant',
        'label' => 'Montant',
        'width' => 80,
        'type'  => 'number'
    ]
];

$totals = [
    'ref'     => 'TOTAL GÉNÉRAL',
    'montant' => 15000.00
];
```

---

## 🚀 Exemple Pratique

```php
// 1. Préparer les colonnes
$headerConfig = [
    [
        'key'   => 'dateDemande',
        'label' => 'Date',
        'width' => 60,
        'type'  => 'date'
    ],
    [
        'key'   => 'client.nom', // Donnée imbriquée
        'label' => 'Client',
        'width' => 150
    ],
    [
        'key'   => 'montantHt',
        'label' => 'Montant HT',
        'width' => 80,
        'type'  => 'number',
        'style' => 'text-align: right;'
    ],
    [
        'key'   => 'statut',
        'label' => 'Statut',
        'width' => 50,
        'styler' => function($value) {
            return $value === 'Urgent' ? 'background-color: red; color: white;' : '';
        }
    ]
];

// 2. Générer le HTML
$html = $this->tableGenerator
    ->setOptions(['empty_message' => 'Aucun enregistrement trouvé.'])
    ->generateTable($headerConfig, $maListeDentities);

// 3. Ajouter au PDF
$pdf->writeHTML($html);
```

---

## 💡 Bonnes Pratiques

1.  **Isolation** : Le service réinitialise ses options après chaque appel à `generateTable()`. Vous pouvez donc l'utiliser plusieurs fois pour différents tableaux dans le même document sans risque de pollution.
2.  **Sécurité** : Grâce au `PropertyAccessor` de Symfony, le service ne plante pas si une propriété est manquante, il retourne simplement une valeur vide.
3.  **Performance** : Pour les très grands tableaux, privilégiez le passage de tableaux associatifs plutôt que des entités Doctrine lourdes pour limiter l'usage mémoire.
