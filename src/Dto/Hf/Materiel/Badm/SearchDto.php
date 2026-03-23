<?php

namespace App\Dto\Hf\Materiel\Badm;

use App\Contract\Dto\PaginationDtoTrait;
use App\Contract\Dto\SearchDtoInterface;
use App\Contract\PaginationDtoInterface;
use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;

class SearchDto implements PaginationDtoInterface, SearchDtoInterface
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




    use PaginationDtoTrait;

    public function __construct()
    {
        $this->sortBy = 'numeroBadm';
    }
}
