<?php

namespace App\Entity\Hf\Materiel\Badm;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\CreatedByTrait;
use App\Entity\Hf\Materiel\Casier\Casier;
use App\Entity\Traits\AgenceServiceTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Admin\Statut\StatutDemande;
use App\Contract\Entity\CreatedByInterface;
use App\Contract\Entity\AgenceServiceInterface;
use App\Repository\Hf\Materiel\Badm\BadmRepository;
use App\Constants\Hf\Materiel\Badm\StatutBadmConstants;

/**
 * @ORM\Entity(repositoryClass=BadmRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Badm implements CreatedByInterface, AgenceServiceInterface
{
    use TimestampableTrait;
    use AgenceServiceTrait;
    use CreatedByTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $numeroBadm;

    /**
     * @ORM\Column(type="integer")
     */
    private $idMateriel;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $motifMateriel;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $etatAchat;

    /**
     * @ORM\Column(type="date")
     */
    private $dateMiseLocation;

    /**
     * @ORM\Column(type="float")
     */
    private $coutAcquisition = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $amortissement = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $valeurNetComptable = 0;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nomClient;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $modalitePaiement;

    /**
     * @ORM\Column(type="float")
     */
    private $prixVenteHt = 0;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $motifMiseRebut;

    /**
     * @ORM\Column(type="integer")
     */
    private $heureMachine;

    /**
     * @ORM\Column(type="integer")
     */
    private $kmMachine;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $numParc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomFichier;

    /**
     * @ORM\ManyToOne(targetEntity=Casier::class, inversedBy="badms")
     */
    private $casierEmetteur;

    /**
     * @ORM\ManyToOne(targetEntity=Casier::class, inversedBy="badms")
     */
    private $casierDestinataire;

    /**
     * @ORM\ManyToOne(targetEntity=TypeMouvement::class, inversedBy="badms")
     */
    private $typeMouvement;

    /**
     * @ORM\ManyToOne(targetEntity=StatutDemande::class, inversedBy="badms")
     */
    private $statutDemande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroBadm(): ?string
    {
        return $this->numeroBadm;
    }

    public function setNumeroBadm(string $numeroBadm): self
    {
        $this->numeroBadm = $numeroBadm;

        return $this;
    }

    public function getIdMateriel(): ?int
    {
        return $this->idMateriel;
    }

    public function setIdMateriel(int $idMateriel): self
    {
        $this->idMateriel = $idMateriel;

        return $this;
    }

    public function getMotifMateriel(): ?string
    {
        return $this->motifMateriel;
    }

    public function setMotifMateriel(?string $motifMateriel): self
    {
        $this->motifMateriel = $motifMateriel;

        return $this;
    }

    public function getEtatAchat(): ?string
    {
        return $this->etatAchat;
    }

    public function setEtatAchat(string $etatAchat): self
    {
        $this->etatAchat = $etatAchat;

        return $this;
    }

    public function getDateMiseLocation(): ?\DateTimeInterface
    {
        return $this->dateMiseLocation;
    }

    public function setDateMiseLocation(\DateTimeInterface $dateMiseLocation): self
    {
        $this->dateMiseLocation = $dateMiseLocation;

        return $this;
    }

    public function getCoutAcquisition(): ?float
    {
        return $this->coutAcquisition;
    }

    public function setCoutAcquisition(float $coutAcquisition): self
    {
        $this->coutAcquisition = $coutAcquisition;

        return $this;
    }

    public function getAmortissement(): ?float
    {
        return $this->amortissement;
    }

    public function setAmortissement(float $amortissement): self
    {
        $this->amortissement = $amortissement;

        return $this;
    }

    public function getValeurNetComptable(): ?float
    {
        return $this->valeurNetComptable;
    }

    public function setValeurNetComptable(float $valeurNetComptable): self
    {
        $this->valeurNetComptable = $valeurNetComptable;

        return $this;
    }

    public function getNomClient(): ?string
    {
        return $this->nomClient;
    }

    public function setNomClient(?string $nomClient): self
    {
        $this->nomClient = $nomClient;

        return $this;
    }

    public function getModalitePaiement(): ?string
    {
        return $this->modalitePaiement;
    }

    public function setModalitePaiement(?string $modalitePaiement): self
    {
        $this->modalitePaiement = $modalitePaiement;

        return $this;
    }

    public function getPrixVenteHt(): ?float
    {
        return $this->prixVenteHt;
    }

    public function setPrixVenteHt(float $prixVenteHt): self
    {
        $this->prixVenteHt = $prixVenteHt;

        return $this;
    }

    public function getMotifMiseRebut(): ?string
    {
        return $this->motifMiseRebut;
    }

    public function setMotifMiseRebut(?string $motifMiseRebut): self
    {
        $this->motifMiseRebut = $motifMiseRebut;

        return $this;
    }

    public function getHeureMachine(): ?int
    {
        return $this->heureMachine;
    }

    public function setHeureMachine(int $heureMachine): self
    {
        $this->heureMachine = $heureMachine;

        return $this;
    }

    public function getKmMachine(): ?int
    {
        return $this->kmMachine;
    }

    public function setKmMachine(int $kmMachine): self
    {
        $this->kmMachine = $kmMachine;

        return $this;
    }

    public function getNumParc(): ?string
    {
        return $this->numParc;
    }

    public function setNumParc(string $numParc): self
    {
        $this->numParc = $numParc;

        return $this;
    }

    public function getNomImage(): ?string
    {
        return $this->nomImage;
    }

    public function setNomImage(?string $nomImage): self
    {
        $this->nomImage = $nomImage;

        return $this;
    }

    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
    }

    public function setNomFichier(?string $nomFichier): self
    {
        $this->nomFichier = $nomFichier;

        return $this;
    }

    public function getCasierEmetteur(): ?Casier
    {
        return $this->casierEmetteur;
    }

    public function setCasierEmetteur(?Casier $casierEmetteur): self
    {
        $this->casierEmetteur = $casierEmetteur;

        return $this;
    }

    public function getCasierDestinataire(): ?Casier
    {
        return $this->casierDestinataire;
    }

    public function setCasierDestinataire(?Casier $casierDestinataire): self
    {
        $this->casierDestinataire = $casierDestinataire;

        return $this;
    }

    public function getTypeMouvement(): ?TypeMouvement
    {
        return $this->typeMouvement;
    }

    public function setTypeMouvement(?TypeMouvement $typeMouvement): self
    {
        $this->typeMouvement = $typeMouvement;

        return $this;
    }

    public function getStatutDemande(): ?StatutDemande
    {
        return $this->statutDemande;
    }

    public function setStatutDemande(?StatutDemande $statutDemande): self
    {
        $this->statutDemande = $statutDemande;

        return $this;
    }
}
