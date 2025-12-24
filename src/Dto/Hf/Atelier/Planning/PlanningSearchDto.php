<?php

namespace App\Dto\Hf\Atelier\Planning;

use App\Entity\Hf\Atelier\Dit\WorNiveauUrgence;

final class PlanningSearchDto
{
    public string $agence;
    public $annee;
    public string $interneExterne = 'TOUS';
    public string $facture = 'ENCOURS';
    public string $plan = 'PLANIFIE';
    public string $dateDebut;
    public $dateFin;
    public $numOr;
    public $numSerie;
    public $idMat;
    public $numParc;
    public $agenceDebite;
    public $serviceDebite;
    public $typeligne = 'TOUETS';
    public $casier;
    public ?WorNiveauUrgence $niveauUrgence = null;
    public $section;
    public $months = 3;
    public ?bool $orBackOrder = false;
    public $typeDocument;
    public $reparationRealise;
    public $orNonValiderDw;
}
