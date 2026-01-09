<?php

namespace App\Dto\Hf\Materiel\Badm;

use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;
use Symfony\Component\Validator\Constraints as Assert;
use App\Constants\Hf\Materiel\Badm\TypeMouvementConstants;

class SecondFormDto
{
    // --------------- Caracteristique du matériel ---------------
    public ?string $designation = null;
    public ?int $idMateriel = null;
    public ?string $numParc = null;
    public ?string $numSerie = null;
    public ?string $groupe = null;
    public ?string $constructeur = null;
    public ?string $modele = null;
    public ?string $anneeDuModele = null;
    public ?string $affectation = null;
    public ?string $dateAchat = null;
    // --------------- Etat machine -----------------
    public ?int $heureMachine = 0;
    public ?int $kmMachine = 0;
    // ---------------- Agence, service et casier emetteur ----------------
    public ?array $emetteur = null;
    // ---------------- Agence, service et casier destinataire ----------------
    public ?array $destinataire = null;
    /**
     * @Assert\NotBlank(message="Le motif ne peut pas être vide.", groups={"motif_materiel"})
     * @Assert\Length(
     *      min=3,
     *      max=100,
     *      minMessage="Le motif doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le motif ne peut pas dépasser {{ limit }} caractères",
     *      groups={"motif_materiel"}
     * )
     */
    public ?string $motifMateriel = null;
    // ---------------- Entrée en parc ----------------
    public ?string $etatAchat = null;
    public ?\DateTime $dateMiseLocation = null;
    // ---------------- Valeur ----------------
    public ?float $coutAcquisition = 0;
    public ?float $amortissement = 0;
    public ?float $valeurNetComptable = 0;

    // ---------------- cession d'actif ----------------
    public ?string $nomClient = null;
    public ?string $modalitePaiement = null;
    public ?float $prixVenteHt = 0;
    // ---------------- Mise au rebut -----------------
    /**
     * @Assert\NotBlank(message="Le motif ne peut pas être vide.", groups={"mise_au_rebut"})
     * @Assert\Length(
     *      min=3,
     *      max=100,
     *      minMessage="Le motif doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le motif ne peut pas dépasser {{ limit }} caractères",
     *      groups={"mise_au_rebut"}
     * )
     */
    public ?string $motifMiseRebut = null;
    public $pieceJoint01 = null; // nomImage
    public $pieceJoint02 = null; // nomFichier
    // --------------- mouvement materiel ---------------
    public TypeMouvement $typeMouvement;
    public \DateTime $dateDemande;
    public string $numeroBadm;
    public StatutDemande $statutDemande;
    public string $mailUser;
    // ---------------- OR -------------------------
    public string $estOr = 'NON';
    public array $ors = [];


    public function getTypeMouvementCssClass(): string
    {
        return TypeMouvementConstants::getCssClass($this->typeMouvement->getDescription());
    }
}
