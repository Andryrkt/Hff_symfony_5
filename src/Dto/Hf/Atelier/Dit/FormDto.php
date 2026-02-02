<?php

namespace App\Dto\Hf\Atelier\Dit;

use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Atelier\Dit\CategorieAteApp;
use App\Entity\Hf\Atelier\Dit\WorTypeDocument;
use App\Entity\Hf\Atelier\Dit\WorNiveauUrgence;
use Symfony\Component\Validator\Constraints as Assert;
use App\Constants\Admin\Historisation\TypeDocumentConstants;

class FormDto
{
    // ---------------- Agence, service emetteur ----------------
    public array $emetteur = [];
    // ---------------- Agence, service débiteur ----------------
    public array $debiteur = [];

    // --------------- info client --------------
    public ?string $nomClient = null;
    public ?string $numeroTel = null;
    public ?string $mailClient = null;
    public ?string $clientSousContrat = null;
    public ?string $numeroClient = null;
    public ?string $libelleClient = null;



    //  --------------- OR ---------------------
    public ?string $numeroOr = null;
    public ?string $statutOr = null;
    public ?\DateTimeInterface $dateOr = null;
    public ?\DateTimeInterface $dateValidationOr = null;

    // ----------------- Devis ----------------------
    public ?string $demandeDevis = null;
    public ?string $numeroDevisRattacher = null;
    public ?string $statutDevis = null;

    // ---------------- Facture -------------------
    public ?string $etatFacturation = null;

    // ---------------- RI -------------------
    public ?string $ri = null;


    // ------------ Annulation ---------------
    public ?bool $estAnnuler = null;
    public ?\DateTimeInterface $dateAnnulation = null;

    // -----------------Reparation ----------------
    /**
     * @Assert\NotBlank(message="le type de réparation doit être sélectionné.")
     */
    public ?string $typeReparation = null;
    /**
     * @Assert\NotBlank(message="le réparation réalisé par doit être sélectionné.")
     */
    public ?string $reparationRealise = null;

    // ----------------Intervention ---------------
    /**
     * @Assert\NotBlank(message="la date ne doit pas être vide")
     */
    public ?\DateTime $datePrevueTravaux = null;
    /**
     * @Assert\NotBlank(message="le niveau d'urgence doit être sélectionné.")
     */
    public ?WorNiveauUrgence $niveauUrgence = null;

    //-----------------Section -----------------
    public ?string $sectionAffectee = null;

    // ----------------- Dit Avoir ----------------------
    public ?string $numeroDemandeDitAvoir = null;
    public ?bool $estDitAvoir = null;

    // ----------------- Dit Refacturation ----------------------
    public ?string $numeroDemandeDitRefacturation = null;
    public ?bool $estDitRefacturation = null;

    // --------------- Piece Joint ----------------------
    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"application/pdf"},
     *     mimeTypesMessage = "Merci de télécharger un fichier PDF valide."
     * )
     */
    public $pieceJoint01 = null;
    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"application/pdf"},
     *     mimeTypesMessage = "Merci de télécharger un fichier PDF valide."
     * )
     */
    public $pieceJoint02 = null;
    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"application/pdf"},
     *     mimeTypesMessage = "Merci de télécharger un fichier PDF valide."
     * )
     */
    public $pieceJoint03 = null;

    // --------------- info sur le DIT ---------------
    public ?string $numeroDit = null;
    public ?string $interneExterne = null;
    /**
     * @Assert\NotBlank(message="L'objet de la demande ne peut pas être vide.")
     * @Assert\Length(
     *      min=5,
     *      max=80,
     *      minMessage="L'objet de la demande doit comporter au moins {{ limit }} caractères",
     *      maxMessage="L'objet de la demande ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?string $objetDemande = null;
    /**
     * @Assert\NotBlank(message="Le detail de la demande ne peut pas être vide.")
     * @Assert\Length(
     *      min=5,
     *      max=1800,
     *      minMessage="Le detail de la demande doit comporter au moins {{ limit }} caractères",
     *      maxMessage="Le detail de la demande ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?string $detailDemande = null;
    public ?string $livraisonPartiel = null;
    public ?string $avisRecouvrement = null;
    /**
     * @Assert\NotBlank(message="le type de document doit être sélectionné.")
     */
    public ?WorTypeDocument $typeDocument = null;
    /**
     * @Assert\NotBlank(message="la catégorie doit être sélectionnée.")
     */
    public ?CategorieAteApp $categorieDemande = null;
    public ?StatutDemande $statutDemande = null;

     // ------ info matériel -----
    /**
     * @Assert\NotBlank(message="L\id materiel ne peut pas être vide.")
     * @Assert\Length(
     *      min=5,
     *      max=5,
     *      minMessage="L\id materiel doit comporter au moins {{ limit }} caractères",
     *      maxMessage="L\id materiel ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    public ?int $idMateriel = null;
    public int $heureMachine = 0;
    public int $kmMachine = 0;
    public ?string $numParc = null;
    public ?string $numSerie = null;

    // ------------------ Autre --------------
    public ?int $numeroMigration = null;
    public ?bool $estAtePolTana = null;
    public ?\DateTimeInterface $dateDemande = null;
    public array $historiqueMateriel = [];
    public ?string $mailDemandeur = null;

    public ?float $coutAcquisition = null;
    public ?float $amortissement = null;
    public ?float $valeurNetComptable = null;
    public ?float $chargeEntretient = null;
    public ?float $chargeLocative = null;
    public ?float $chiffreAffaire = null;
    public ?float $resultatExploitation = null;

    public ?string $modele = null;
    public ?string $designation = null;
    public ?string $constructeur = null;
    public ?string $casier = null;

    public function getValeurNetComptable(): float
    {
        return $this->valeurNetComptable = $this->coutAcquisition - $this->amortissement;
    }

    public function getResultatExploitation(): float
    {
        return $this->resultatExploitation = $this->chiffreAffaire - $this->chargeLocative - $this->chargeEntretient;
    }
}
