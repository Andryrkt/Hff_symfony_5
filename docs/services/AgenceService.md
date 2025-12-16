# Documentation AgenceService - Traçabilité des Agences et Services

## 📋 Vue d'ensemble

Ce système permet de tracer les agences et services émetteurs et débiteurs pour les entités de votre application Symfony (comme les DOM, Indemnités, etc.).

## 🏗️ Architecture

### Composants

1. **[AgencyServiceAwareInterface](file:///d:/hff_symfony_5/src/Contract/Entity/AgencyServiceAwareInterface.php)** - Contrat que les entités doivent implémenter
2. **[AgenceServiceTrait](file:///d:/hff_symfony_5/src/Entity/Traits/AgenceServiceTrait.php)** - Implémentation réutilisable avec propriétés Doctrine

## 🚀 Utilisation

### Appliquer à une entité

```php
<?php

namespace App\Entity;

use App\Contract\Entity\AgencyServiceAwareInterface;
use App\Entity\Traits\AgenceServiceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class VotreEntite implements AgencyServiceAwareInterface
{
    use AgenceServiceTrait;
    
    // Vos autres propriétés...
}
```

### Migration de la base de données

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## 📝 Propriétés ajoutées

Le trait ajoute 4 propriétés à votre entité :

| Propriété | Type | Colonne DB | Description |
|-----------|------|------------|-------------|
| `agenceEmetteurId` | `?Agence` | `agence_emetteur_id` | Agence émettrice |
| `serviceEmetteurId` | `?Service` | `service_emetteur_id` | Service émetteur |
| `agenceDebiteurId` | `?Agence` | `agence_debiteur_id` | Agence débitrice |
| `serviceDebiteur` | `?Service` | `service_debiteur_id` | Service débiteur |

## 💡 Exemple d'utilisation

```php
// Dans un contrôleur
$dom = new Dom();

// Définir l'agence et service émetteurs
$dom->setAgenceEmetteurId($agenceEmetteur);
$dom->setServiceEmetteurId($serviceEmetteur);

// Définir l'agence et service débiteurs
$dom->setAgenceDebiteurId($agenceDebiteur);
$dom->setServiceDebiteur($serviceDebiteur);

$entityManager->persist($dom);
$entityManager->flush();

// Récupérer les valeurs
$agence = $dom->getAgenceEmetteurId();
$service = $dom->getServiceEmetteurId();
```

## ⚠️ Convention de nommage

Les colonnes en base de données utilisent la convention snake_case avec suffixe `_id` :
- `agence_emetteur_id`
- `service_emetteur_id`
- `agence_debiteur_id`
- `service_debiteur_id`

Toutes les colonnes sont **nullable** pour permettre une flexibilité maximale.

## 🎯 Entités candidates

Entités qui devraient probablement implémenter ce contrat :
- `Dom`
- `Indemnite`
- Toute entité métier nécessitant un suivi des agences/services
