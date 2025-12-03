<?php

namespace App\Dto\Hf\Rh\Dom;

use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use App\Entity\Admin\Statut\StatutDemande;

use App\Contract\PaginationDtoInterface;

class DomSearchDto implements PaginationDtoInterface
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

    // Pagination et tri
    public int $limit = 50;
    public string $sortBy = 'numeroOrdreMission';
    public string $sortOrder = 'DESC';

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function setSortBy(string $sortBy): self
    {
        $this->sortBy = $sortBy;
        return $this;
    }

    public function setSortOrder(string $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
}
