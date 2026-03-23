# Documentation CreatedBy - Traçabilité des Créateurs

## 📋 Vue d'ensemble

Ce système permet de tracer automatiquement quel utilisateur a créé une entité dans votre application Symfony.

## 🏗️ Architecture

### Composants

1. **[CreatedByInterface](file:///d:/hff_symfony_5/src/Contract/CreatedByInterface.php)** - Contrat que les entités doivent implémenter
2. **[CreatedByTrait](file:///d:/hff_symfony_5/src/Entity/Trait/CreatedByTrait.php)** - Implémentation réutilisable avec propriété Doctrine
3. **[CreatedByListener](file:///d:/hff_symfony_5/src/EventListener/CreatedByListener.php)** - Injection automatique de l'utilisateur connecté

## 🚀 Utilisation

### Étape 1 : Configuration du service

Ajoutez dans `config/services.yaml` :

```yaml
services:
    App\EventListener\CreatedByListener:
        tags:
            - { name: doctrine.event_listener, event: prePersist }
```

### Étape 2 : Appliquer à une entité

```php
<?php

namespace App\Entity;

use App\Contract\CreatedByInterface;
use App\Entity\Trait\CreatedByTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class VotreEntite implements CreatedByInterface
{
    use CreatedByTrait;
    
    // Vos autres propriétés...
}
```

### Étape 3 : Migration de la base de données

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## ✨ Fonctionnement

- **Automatique** : Lors de la création (`persist`), le listener injecte automatiquement l'utilisateur connecté
- **Manuel possible** : Vous pouvez toujours définir manuellement avec `setCreatedBy()`
- **Nullable** : La colonne est nullable pour gérer les cas où il n'y a pas d'utilisateur connecté

## 📝 Exemple d'utilisation dans un contrôleur

```php
// Création automatique
$entity = new VotreEntite();
$entityManager->persist($entity);
$entityManager->flush();
// $entity->getCreatedBy() contiendra automatiquement l'utilisateur connecté

// Définition manuelle (si nécessaire)
$entity = new VotreEntite();
$entity->setCreatedBy($autreUtilisateur);
$entityManager->persist($entity);
```

## 🎯 Entités candidates

Entités qui devraient probablement implémenter ce contrat :
- `Dom`
- `Indemnite`
- `Personnel`
- Toute entité métier créée par des utilisateurs

## ⚠️ Convention de nommage

La colonne en base de données sera nommée `createdBy` (camelCase) conformément à votre convention de nommage existante.
