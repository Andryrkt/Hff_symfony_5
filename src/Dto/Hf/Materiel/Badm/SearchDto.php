<?php

namespace App\Dto\Hf\Materiel\Badm;

use App\Contract\PaginationDtoInterface;
use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;

class SearchDto implements PaginationDtoInterface
{
    public ?StatutDemande $statut = null;
    public ?TypeMouvement $typeMouvement = null;
    public $idMateriel = null;
    public $numParc = null;
    public $numSerie = null;
    public $numeroBadm = null;

    public array $dateDemande = [];
    public array $debiteur = [];
    public array $emetteur = [];




    // Pagination et tri
    public int $limit = 50;
    public string $sortBy = 'numeroBadm';
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
