<?php

namespace App\Dto\Hf\Rh\Dom;

use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use App\Entity\Admin\Statut\StatutDemande;

class DomSearchDto
{
    public ?StatutDemande $statut = null;
    public ?SousTypeDocument $sousTypeDocument = null;
    public ?string $numDom = null;
    public ?string $matricule = null;
    public array $dateDemande = [];
    public array $dateMission = [];

    public ?array $debiteur = null;
    public ?array $emetteur = null;
    public bool $pieceJustificatif;
}
