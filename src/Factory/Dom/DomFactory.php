<?php

namespace App\Factory\Dom;

use App\Dto\Dom\DomFirstFormData;
use App\Entity\Dom\DemandeOrdreMission;

class DomFactory
{
    // This class can be used to create instances of DOM-related entities or DTOs.
    // It can include methods to initialize entities with default values or to handle complex creation logic.

    public function createDomFirstFormData(DomFirstFormData $dto): DemandeOrdreMission
    {
        $dom = new DemandeOrdreMission();
        $dom->setDomSousTypeDocument($dto->sousTypeDocument);


        return $dom;
    }

    // Additional factory methods can be added here for other DOM-related entities or DTOs.
}
