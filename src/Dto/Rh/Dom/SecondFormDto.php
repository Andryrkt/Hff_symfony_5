<?php

namespace App\Dto\Rh\Dom;

use App\Entity\Rh\Dom\Categorie;
use App\Entity\Rh\Dom\Rmq;
use App\Entity\Rh\Dom\Site;
use App\Entity\Rh\Dom\SousTypeDocument;
use DateTime;

class SecondFormDto
{
    public ?array $debiteur = null;
    public ?string $agenceUser = null;
    public ?string $serviceUser = null;
    public ?DateTime $dateDemande = null;
    public ?SousTypeDocument $typeMission = null;
    public ?Categorie $categorie = null;
    public ?Site $site = null;
    public ?string $matricule = null;
    public ?string $nom = null;
    public ?string $prenom = null;
    public ?string $cin = null;

    public ?string $salarier = null;
    public ?Rmq $rmq = null;


    public ?array $dateHeureMission = null;
    public $nombreJour = null;
    public ?string $motifDeplacement = null;
    public ?bool $pieceJustificatif = null;
    public ?string $client = null;
    public ?string $fiche = null;
    public ?string $lieuIntervention = null;
    public ?string $vehiculeSociete = null;
    public ?string $numVehicule = null;
    public $idemnityDepl = null;
    public $totalIndemniteDeplacement = null;
    public ?string $devis = null;
    public $supplementJournaliere = null;
    public ?string $indemniteForfaitaire = null;
    public $totalIndemniteForfaitaire = null;
    public ?string $motifAutresDepense1 = null;
    public $autresDepense1 = null;
    public ?string $motifAutresDepense2 = null;
    public $autresDepense2 = null;
    public ?string $motifAutresDepense3 = null;
    public $autresDepense3 = null;
    public $totalAutresDepenses = null;
    public $totalGeneralPayer = null;
    public ?string $modePayement = null;
    public ?string $mode = null;
    public $pieceJoint01 = null;
    public $pieceJoint02 = null;

    // autre
    public string $numeroOrdreMission;
    public string $mailUser;
}
