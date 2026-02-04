<?php

namespace App\Dto\Hf\Atelier\Dit;

use App\Contract\Dto\PaginationDtoTrait;
use App\Contract\Dto\SearchDtoInterface;
use App\Contract\PaginationDtoInterface;
use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Atelier\Dit\WorTypeDocument;
use App\Entity\Hf\Atelier\Dit\WorNiveauUrgence;


class SearchDto implements PaginationDtoInterface, SearchDtoInterface
{
    use PaginationDtoTrait;

    public function __construct()
    {
        $this->sortBy = 'numeroDit';
    }

    public ?WorNiveauUrgence $niveauUrgence = null;
    public ?StatutDemande $statut = null;
    public ?int $idMateriel = 0;
    public ?WorTypeDocument $typeDocument = null;
    public ?string $internetExterne = '';
    public ?string $numParc = '';
    public ?string $numSerie = '';
    public ?string $numDit = '';
    public ?int $numOr = null;
    public ?string $statutOr = '';
    public ?bool $ditSansOr = false;
    public  $categorie;
    public ?string $utilisateur = '';
    public ?string $sectionAffectee = null;
    public ?string $sectionSupport1 = '';
    public ?string $sectionSupport2 = '';
    public ?string $sectionSupport3 = '';
    public ?string $etatFacture = '';
    public ?string $numDevis = '';
    public $reparationRealise;
    public array $dateDemande = [];
    public array $debiteur = [];
    public array $emetteur = [];
}
