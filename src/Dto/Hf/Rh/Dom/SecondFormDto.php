<?php

namespace App\Dto\Hf\Rh\Dom;

use App\Entity\Hf\Rh\Dom\Categorie;
use App\Entity\Hf\Rh\Dom\Rmq;
use App\Entity\Hf\Rh\Dom\Site;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

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

    /**
     * @Assert\NotBlank(message="Le motif de déplacement ne peut pas être vide.")
     * @Assert\Length(
     *      min=3,
     *      max=100,
     *      minMessage="Le motif de déplacement doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le motif de déplacement ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?string $motifDeplacement = null;

    public ?bool $pieceJustificatif = null;

    /**
     * @Assert\NotBlank(message="Le client ne peut pas être vide.")
     * @Assert\Length(
     *      min=3,
     *      max=50,
     *      minMessage="Le client doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le client ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?string $client = null;

    public ?string $fiche = null;

    /**
     * @Assert\NotBlank(message="Le lieu d'intervention ne peut pas être vide.")
     * @Assert\Length(
     *      min=3,
     *      max=100,
     *      minMessage="Le lieu doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le lieu ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?string $lieuIntervention = null;

    public ?string $vehiculeSociete = null;

    /**
     * @Assert\Length(
     *      min=3,
     *      max=10,
     *      minMessage="Le n° véhicule doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le n° véhicule ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?string $numVehicule = null;

    public $idemnityDepl = null;
    public $totalIndemniteDeplacement = null;
    public ?string $devis = null;
    public $supplementJournaliere = null;
    public ?string $indemniteForfaitaire = null;
    public $totalIndemniteForfaitaire = null;

    /**
     * @Assert\Length(
     *      min=3,
     *      max=30,
     *      minMessage="Le motif autre dépense 1 doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le motif autre dépense 1 ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?string $motifAutresDepense1 = null;
    public $autresDepense1 = null;

    /**
     * @Assert\Length(
     *      min=3,
     *      max=30,
     *      minMessage="Le motif autre dépense 2 doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le motif autre dépense 2 ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?string $motifAutresDepense2 = null;
    public $autresDepense2 = null;

    /**
     * @Assert\Length(
     *      min=3,
     *      max=30,
     *      minMessage="Le motif autre dépense 3 doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le motif autre dépense 3 ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?string $motifAutresDepense3 = null;
    public $autresDepense3 = null;

    public $totalAutresDepenses = null;

    /**
     * @Assert\NotBlank(message="Le montant total ne peut pas être vide.")
     */
    public $totalGeneralPayer = null;

    public ?string $modePayement = null;

    /**
     * @Assert\Length(
     *      min=3,
     *      max=30,
     *      minMessage="Le mode doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le mode ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?string $mode = null;
    public $pieceJoint01 = null;
    public $pieceJoint02 = null;

    // autre
    public string $numeroOrdreMission;
    public string $mailUser = '';
}
