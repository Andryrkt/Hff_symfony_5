<?php

namespace App\Dto\Hf\Rh\Dom;

use App\Contract\Dto\PaginationDtoTrait;
use App\Contract\Dto\SearchDtoInterface;
use App\Contract\PaginationDtoInterface;

use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;

class DomSearchDto implements PaginationDtoInterface, SearchDtoInterface
{
    public ?StatutDemande $statut = null;
    public ?SousTypeDocument $sousTypeDocument = null;
    public ?string $numDom = null;
    public ?string $matricule = null;
    public ?bool $pieceJustificatif = null;
    public array $dateMission = [];

    public array $dateDemande = [];
    public array $debiteur = [];
    public array $emetteur = [];

    use PaginationDtoTrait;

    public function __construct()
    {
        $this->sortBy = 'numeroOrdreMission';
    }
}
