<?php

namespace App\Entity\Rh\Dom;

use App\Entity\Rh\Dom\Site;
use App\Entity\Rh\Dom\Categorie;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Rh\Dom\SousTypeDocument;
use App\Repository\Rh\Dom\DomRepository;
use App\Entity\Traits\AgenceServiceTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Admin\Statut\StatutDemande;

/**
 * @ORM\Entity(repositoryClass=DomRepository::class)
 * @ORM\Table(name="Demande_ordre_mission")
 * @ORM\HasLifecycleCallbacks
 */
class Dom
{
    use TimestampableTrait;
    use AgenceServiceTrait;

    public const CODE_APPLICATION = 'DOM';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $numeroOrdreMission;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $matricule;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nomSessionUtilisateur;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $heureDebut;

    /**
     * @ORM\Column(type="date")
     */
    private $dateFin;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $heureFin;

    /**
     * @ORM\Column(type="smallint")
     */
    private $nombreJour;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $motifDeplacement;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lieuIntervention;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $vehiculeSociete;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $indemniteForfaitaire;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $totalIndemniteForfaitaire;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $motifAutreDepense1;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $autresDepense1;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $motifAutresDepense2;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $autresDepense2;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $motifAutresDepense3;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $autresDepense3;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $totalAutresDepenses;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $totalGeneralPayer;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $modePayement;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $pieceJoint01;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $pieceJoint02;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $pieceJoint3; // TODO à supprimer

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $codeStatut;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $numeroTel;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $devis;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $libelleCodeAgenceService;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $fiche;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $numVehicule;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $droitIndemnite; //! supplement jounalier

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $idemnityDepl;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $emetteur; // TODO: à suprimer si c'est pas necessaire

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $debiteur; // TODO: à suprimer si c'est pas necessaire

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateHeureModifStatut;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateDemande;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pieceJustificatif; // TODO: à verifier

    /**
     * @ORM\ManyToOne(targetEntity=StatutDemande::class, inversedBy="doms")
     */
    private $idStatutDemande;

    /**
     * @ORM\ManyToOne(targetEntity=SousTypeDocument::class, inversedBy="doms", cascade={"persist"})
     */
    private $sousTypeDocument;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="doms")
     */
    private $siteId;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="doms", cascade={"persist"})
     */
    private $categoryId;

    /** ====================================================================
     * GETTERS & SETTERS
     *====================================================================*/


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroOrdreMission(): ?string
    {
        return $this->numeroOrdreMission;
    }

    public function setNumeroOrdreMission(string $numeroOrdreMission): self
    {
        $this->numeroOrdreMission = $numeroOrdreMission;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getNomSessionUtilisateur(): ?string
    {
        return $this->nomSessionUtilisateur;
    }

    public function setNomSessionUtilisateur(string $nomSessionUtilisateur): self
    {
        $this->nomSessionUtilisateur = $nomSessionUtilisateur;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getHeureDebut(): ?string
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(string $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getHeureFin(): ?string
    {
        return $this->heureFin;
    }

    public function setHeureFin(string $heureFin): self
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    public function getNombreJour(): ?int
    {
        return $this->nombreJour;
    }

    public function setNombreJour(int $nombreJour): self
    {
        $this->nombreJour = $nombreJour;

        return $this;
    }

    public function getMotifDeplacement(): ?string
    {
        return $this->motifDeplacement;
    }

    public function setMotifDeplacement(string $motifDeplacement): self
    {
        $this->motifDeplacement = $motifDeplacement;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getLieuIntervention(): ?string
    {
        return $this->lieuIntervention;
    }

    public function setLieuIntervention(string $lieuIntervention): self
    {
        $this->lieuIntervention = $lieuIntervention;

        return $this;
    }

    public function getVehiculeSociete(): ?string
    {
        return $this->vehiculeSociete;
    }

    public function setVehiculeSociete(string $vehiculeSociete): self
    {
        $this->vehiculeSociete = $vehiculeSociete;

        return $this;
    }

    public function getIndemniteForfaitaire(): ?string
    {
        return $this->indemniteForfaitaire;
    }

    public function setIndemniteForfaitaire(?string $indemniteForfaitaire): self
    {
        $this->indemniteForfaitaire = $indemniteForfaitaire;

        return $this;
    }

    public function getTotalIndemniteForfaitaire(): ?string
    {
        return $this->totalIndemniteForfaitaire;
    }

    public function setTotalIndemniteForfaitaire(string $totalIndemniteForfaitaire): self
    {
        $this->totalIndemniteForfaitaire = $totalIndemniteForfaitaire;

        return $this;
    }

    public function getMotifAutreDepense1(): ?string
    {
        return $this->motifAutreDepense1;
    }

    public function setMotifAutreDepense1(?string $motifAutreDepense1): self
    {
        $this->motifAutreDepense1 = $motifAutreDepense1;

        return $this;
    }

    public function getAutresDepense1(): ?string
    {
        return $this->autresDepense1;
    }

    public function setAutresDepense1(?string $autresDepense1): self
    {
        $this->autresDepense1 = $autresDepense1;

        return $this;
    }

    public function getMotifAutresDepense2(): ?string
    {
        return $this->motifAutresDepense2;
    }

    public function setMotifAutresDepense2(?string $motifAutresDepense2): self
    {
        $this->motifAutresDepense2 = $motifAutresDepense2;

        return $this;
    }

    public function getAutresDepense2(): ?string
    {
        return $this->autresDepense2;
    }

    public function setAutresDepense2(?string $autresDepense2): self
    {
        $this->autresDepense2 = $autresDepense2;

        return $this;
    }

    public function getMotifAutresDepense3(): ?string
    {
        return $this->motifAutresDepense3;
    }

    public function setMotifAutresDepense3(?string $motifAutresDepense3): self
    {
        $this->motifAutresDepense3 = $motifAutresDepense3;

        return $this;
    }

    public function getAutresDepense3(): ?string
    {
        return $this->autresDepense3;
    }

    public function setAutresDepense3(?string $autresDepense3): self
    {
        $this->autresDepense3 = $autresDepense3;

        return $this;
    }

    public function getTotalAutresDepenses(): ?string
    {
        return $this->totalAutresDepenses;
    }

    public function setTotalAutresDepenses(?string $totalAutresDepenses): self
    {
        $this->totalAutresDepenses = $totalAutresDepenses;

        return $this;
    }

    public function getTotalGeneralPayer(): ?string
    {
        return $this->totalGeneralPayer;
    }

    public function setTotalGeneralPayer(?string $totalGeneralPayer): self
    {
        $this->totalGeneralPayer = $totalGeneralPayer;

        return $this;
    }

    public function getModePayement(): ?string
    {
        return $this->modePayement;
    }

    public function setModePayement(?string $modePayement): self
    {
        $this->modePayement = $modePayement;

        return $this;
    }

    public function getPieceJoint01(): ?string
    {
        return $this->pieceJoint01;
    }

    public function setPieceJoint01(?string $pieceJoint01): self
    {
        $this->pieceJoint01 = $pieceJoint01;

        return $this;
    }

    public function getPieceJoint02(): ?string
    {
        return $this->pieceJoint02;
    }

    public function setPieceJoint02(?string $pieceJoint02): self
    {
        $this->pieceJoint02 = $pieceJoint02;

        return $this;
    }

    public function getPieceJoint3(): ?string
    {
        return $this->pieceJoint3;
    }

    public function setPieceJoint3(?string $pieceJoint3): self
    {
        $this->pieceJoint3 = $pieceJoint3;

        return $this;
    }

    public function getCodeStatut(): ?string
    {
        return $this->codeStatut;
    }

    public function setCodeStatut(?string $codeStatut): self
    {
        $this->codeStatut = $codeStatut;

        return $this;
    }

    public function getNumeroTel(): ?string
    {
        return $this->numeroTel;
    }

    public function setNumeroTel(?string $numeroTel): self
    {
        $this->numeroTel = $numeroTel;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDevis(): ?string
    {
        return $this->devis;
    }

    public function setDevis(?string $devis): self
    {
        $this->devis = $devis;

        return $this;
    }

    public function getLibelleCodeAgenceService(): ?string
    {
        return $this->libelleCodeAgenceService;
    }

    public function setLibelleCodeAgenceService(?string $libelleCodeAgenceService): self
    {
        $this->libelleCodeAgenceService = $libelleCodeAgenceService;

        return $this;
    }

    public function getFiche(): ?string
    {
        return $this->fiche;
    }

    public function setFiche(?string $fiche): self
    {
        $this->fiche = $fiche;

        return $this;
    }

    public function getNumVehicule(): ?string
    {
        return $this->numVehicule;
    }

    public function setNumVehicule(?string $numVehicule): self
    {
        $this->numVehicule = $numVehicule;

        return $this;
    }

    public function getDroitIndemnite(): ?string
    {
        return $this->droitIndemnite;
    }

    public function setDroitIndemnite(?string $droitIndemnite): self
    {
        $this->droitIndemnite = $droitIndemnite;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(?string $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getIdemnityDepl(): ?string
    {
        return $this->idemnityDepl;
    }

    public function setIdemnityDepl(?string $idemnityDepl): self
    {
        $this->idemnityDepl = $idemnityDepl;

        return $this;
    }

    public function getEmetteur(): ?string
    {
        return $this->emetteur;
    }

    public function setEmetteur(?string $emetteur): self
    {
        $this->emetteur = $emetteur;

        return $this;
    }

    public function getDebiteur(): ?string
    {
        return $this->debiteur;
    }

    public function setDebiteur(?string $debiteur): self
    {
        $this->debiteur = $debiteur;

        return $this;
    }

    public function getIdStatutDemande(): ?StatutDemande
    {
        return $this->idStatutDemande;
    }

    public function setIdStatutDemande(?StatutDemande $idStatutDemande): self
    {
        $this->idStatutDemande = $idStatutDemande;

        return $this;
    }

    public function getDateHeureModifStatut(): ?\DateTimeInterface
    {
        return $this->dateHeureModifStatut;
    }

    public function setDateHeureModifStatut(?\DateTimeInterface $dateHeureModifStatut): self
    {
        $this->dateHeureModifStatut = $dateHeureModifStatut;

        return $this;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->dateDemande;
    }

    public function setDateDemande(?\DateTimeInterface $dateDemande): self
    {
        $this->dateDemande = $dateDemande;

        return $this;
    }

    public function getSousTypeDocument(): ?SousTypeDocument
    {
        return $this->sousTypeDocument;
    }

    public function setSousTypeDocument(?SousTypeDocument $sousTypeDocument): self
    {
        $this->sousTypeDocument = $sousTypeDocument;

        return $this;
    }

    public function getPieceJustificatif(): ?bool
    {
        return $this->pieceJustificatif;
    }

    public function setPieceJustificatif(?bool $pieceJustificatif): self
    {
        $this->pieceJustificatif = $pieceJustificatif;

        return $this;
    }

    public function getSiteId(): ?Site
    {
        return $this->siteId;
    }

    public function setSiteId(?Site $siteId): self
    {
        $this->siteId = $siteId;

        return $this;
    }

    public function getCategoryId(): ?Categorie
    {
        return $this->categoryId;
    }

    public function setCategoryId(?Categorie $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }
}
