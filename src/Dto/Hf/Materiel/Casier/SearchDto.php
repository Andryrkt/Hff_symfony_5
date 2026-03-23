<?php

namespace App\Dto\Hf\Materiel\Casier;

use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\Statut\StatutDemande;

class SearchDto
{
    public ?Agence $agence = null;
    public ?string $casier = null;
    public ?StatutDemande $statut = null;
}
