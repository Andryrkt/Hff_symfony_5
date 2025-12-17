# DatabaseInformix

Le service `App\Model\DatabaseInformix` permet de gérer la connexion à une base de données Informix via ODBC. Il implémente l'interface `App\Contract\DatabaseConnectionInterface`.

## Configuration

Ce service nécessite que les variables d'environnement suivantes soient définies dans votre fichier `.env` ou `.env.local` :

- `DB_DNS_INFORMIX` : Le DSN (Data Source Name) pour la connexion ODBC.
- `DB_USERNAME_INFORMIX` : Le nom d'utilisateur pour la base de données.
- `DB_PASSWORD_INFORMIX` : Le mot de passe pour la base de données.

Exemple de configuration dans `.env` :

```bash
DB_DNS_INFORMIX=Driver={IBM INFORMIX ODBC DRIVER};SERVER=mon_serveur;DATABASE=ma_base;HOST=192.168.1.1;SERVICE=9088;PROTOCOL=onsoctcp;
DB_USERNAME_INFORMIX=mon_user
DB_PASSWORD_INFORMIX=mon_password
```

## Utilisation

Ce service est conçu pour être injecté via le conteneur de services de Symfony.

### Injection de dépendance

Vous pouvez injecter le service dans vos constructeurs :

```php
use App\Model\DatabaseInformix;

class MonService
{
    private $database;

    public function __construct(DatabaseInformix $database)
    {
        $this->database = $database;
    }
    
    // ...
}
```

### Méthodes disponibles

#### `connect()`

Établit la connexion à la base de données.

```php
try {
    $conn = $this->database->connect();
} catch (\Exception $e) {
    // Gestion de l'erreur
}
```

#### `executeQuery(string $query)`

Exécute une requête SQL brute. Assurez-vous d'avoir appelé `connect()` au préalable.

```php
$sql = "SELECT * FROM ma_table";
try {
    $result = $this->database->executeQuery($sql);
} catch (\Exception $e) {
    // Gestion de l'erreur
}
```

#### `fetchResults($result)`

Récupère les résultats d'une requête exécutée sous forme de tableau associatif.

```php
$rows = $this->database->fetchResults($result);
foreach ($rows as $row) {
    echo $row['nom_colonne'];
}
```

#### `close()`

Ferme la connexion à la base de données.

```php
$this->database->close();
```

## Gestion des Erreurs

Les méthodes `connect()` et `executeQuery()` lancent des exceptions `\Exception` en cas d'échec. Les erreurs sont également enregistrées via le `LoggerInterface` injecté (canaux de log Symfony).
