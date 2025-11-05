<?php

namespace App\Dto\Rh\Dom;

use App\Entity\Rh\Dom\Categorie;
use App\Entity\Rh\Dom\Rmq;
use App\Entity\Rh\Dom\Site;
use App\Entity\Rh\Dom\SousTypeDocument;
use DateTime;

class SecondFormDto
{
    public array $debiteur;
    public string $agenceUser;
    public string $serviceUser;
    public DateTime $dateDemande;
    public ?SousTypeDocument $typeMission;
    public ?Categorie $categorie;
    public ?Site $site;
    public ?string $matricule;
    public ?string $nom;
    public ?string $prenom;
    public ?string $cin;
    
    public ?string $salarier;
    public ?Rmq $rmq;


    public string $dateHeureMission;
    public $nombreJour;
    public string $motifDeplacement;
    public bool $pieceJustificatif;
    public string $client;
    public string $fiche;
    public string $lieuIntervention;
    public string $vehiculeSociete;
    public string $numVehicule;
    public $idemnityDepl;
    public $totalIndemniteDeplacement;
    public string $devis;
    public $supplementJournaliere;
    public string $indemniteForfaitaire;
    public $totalIndemniteForfaitaire;
    public string $motifAutresDepense1;
    public $autresDepense1;
    public string $motifAutresDepense2;
    public $autresDepense2;
    public string $motifAutresDepense3;
    public $autresDepense3;
    public $totalAutresDepenses;
    public $totalGeneralPayer;
    public string $modePayement;
    public string $mode;
    public $pieceJoint01;
    public $pieceJoint02;
}
