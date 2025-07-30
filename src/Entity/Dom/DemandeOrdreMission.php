<?php

namespace App\Entity\Dom;

use App\Contract\AgencyServiceAwareInterface;
use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Admin\PersonnelUser\User;
use App\Repository\Dom\DemandeOrdreMissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Expression;
use \App\Entity\Admin\Statut\StatutDemande;
use \App\Entity\Admin\AgenceService\Agence;
use \App\Entity\Admin\AgenceService\Service;

/**
 * @ORM\Entity(repositoryClass=DemandeOrdreMissionRepository::class)
 * @Assert\Expression(
 *     "this.getDateFinMission() > this.getDateDebutMission()",
 *     message="La date de fin de mission doit être postérieure à la date de début."
 * )
 * @ORM\HasLifecycleCallbacks
 */
class DemandeOrdreMission implements AgencyServiceAwareInterface
{
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="demandeOrdreMissions")
     */
    private $domDemandeur;

    /**
     * @ORM\ManyToOne(targetEntity=Personnel::class, inversedBy="demandeOrdreMissions")
     */
    private $domPersonnel;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateDebutMission;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFinMission;

    /**
     * @ORM\ManyToOne(targetEntity=DomSousTypeDocument::class, inversedBy="demandeOrdreMissions")
     */
    private $domSousTypeDocument;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $nombreJours;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motifDeplacement;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lieuIntervention;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $vehiculeSociete;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $motifAutresDepense1;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montantAutresDepense1;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $motifAutresDepense2;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montantAutresDepense2;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $motifAutresDepense3;

    /**
     * @ORM\Column(type="float")
     */
    private $montantAutresDepense3;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $indemniteForfaitaire;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalIndemniteForfaitaire;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalGeneralPayer;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $modePaiement;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $pieceJointe1;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $pieceJointe2;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $codeStatut;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $numeroTel;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $devis;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $fiche;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $numeroVehicule;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $supplementJournalier;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $indemniteChantier;

    /**
     * @ORM\ManyToOne(targetEntity=StatutDemande::class, inversedBy="demandeOrdreMissions")
     */
    private $statutDemande_id;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="domEmetteur")
     */
    private $agenceEmetteurId;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="domServiceEmetteur")
     */
    private $serviceEmetteurId;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="domDebiteur")
     */
    private $agenceDebiteurId;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="domServiceDebiteur")
     */
    private $serviceDebiteurId;

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

    public function getDomDemandeur(): ?User
    {
        return $this->domDemandeur;
    }

    public function setDomDemandeur(?User $domDemandeur): self
    {
        $this->domDemandeur = $domDemandeur;

        return $this;
    }

    public function getDomPersonnel(): ?Personnel
    {
        return $this->domPersonnel;
    }

    public function setDomPersonnel(?Personnel $domPersonnel): self
    {
        $this->domPersonnel = $domPersonnel;

        return $this;
    }

    public function getDateDebutMission(): ?\DateTimeInterface
    {
        return $this->dateDebutMission;
    }

    public function setDateDebutMission(?\DateTimeInterface $dateDebutMission): self
    {
        $this->dateDebutMission = $dateDebutMission;

        return $this;
    }

    public function getDateFinMission(): ?\DateTimeInterface
    {
        return $this->dateFinMission;
    }

    public function setDateFinMission(?\DateTimeInterface $dateFinMission): self
    {
        $this->dateFinMission = $dateFinMission;

        return $this;
    }

    public function getDomSousTypeDocument(): ?DomSousTypeDocument
    {
        return $this->domSousTypeDocument;
    }

    public function setDomSousTypeDocument(?DomSousTypeDocument $domSousTypeDocument): self
    {
        $this->domSousTypeDocument = $domSousTypeDocument;

        return $this;
    }

    public function getNombreJours(): ?int
    {
        return $this->nombreJours;
    }

    public function setNombreJours(?int $nombreJours): self
    {
        $this->nombreJours = $nombreJours;

        return $this;
    }

    public function getMotifDeplacement(): ?string
    {
        return $this->motifDeplacement;
    }

    public function setMotifDeplacement(?string $motifDeplacement): self
    {
        $this->motifDeplacement = $motifDeplacement;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(?string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getLieuIntervention(): ?string
    {
        return $this->lieuIntervention;
    }

    public function setLieuIntervention(?string $lieuIntervention): self
    {
        $this->lieuIntervention = $lieuIntervention;

        return $this;
    }

    public function getVehiculeSociete(): ?string
    {
        return $this->vehiculeSociete;
    }

    public function setVehiculeSociete(?string $vehiculeSociete): self
    {
        $this->vehiculeSociete = $vehiculeSociete;

        return $this;
    }

    public function getMotifAutresDepense1(): ?string
    {
        return $this->motifAutresDepense1;
    }

    public function setMotifAutresDepense1(?string $motifAutresDepense1): self
    {
        $this->motifAutresDepense1 = $motifAutresDepense1;

        return $this;
    }

    public function getMontantAutresDepense1(): ?float
    {
        return $this->montantAutresDepense1;
    }

    public function setMontantAutresDepense1(?float $montantAutresDepense1): self
    {
        $this->montantAutresDepense1 = $montantAutresDepense1;

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

    public function getMontantAutresDepense2(): ?float
    {
        return $this->montantAutresDepense2;
    }

    public function setMontantAutresDepense2(?float $montantAutresDepense2): self
    {
        $this->montantAutresDepense2 = $montantAutresDepense2;

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

    public function getMontantAutresDepense3(): ?float
    {
        return $this->montantAutresDepense3;
    }

    public function setMontantAutresDepense3(float $montantAutresDepense3): self
    {
        $this->montantAutresDepense3 = $montantAutresDepense3;

        return $this;
    }

    public function getIndemniteForfaitaire(): ?float
    {
        return $this->indemniteForfaitaire;
    }

    public function setIndemniteForfaitaire(?float $indemniteForfaitaire): self
    {
        $this->indemniteForfaitaire = $indemniteForfaitaire;

        return $this;
    }

    public function getTotalIndemniteForfaitaire(): ?float
    {
        return $this->totalIndemniteForfaitaire;
    }

    public function setTotalIndemniteForfaitaire(?float $totalIndemniteForfaitaire): self
    {
        $this->totalIndemniteForfaitaire = $totalIndemniteForfaitaire;

        return $this;
    }

    public function getTotalGeneralPayer(): ?float
    {
        return $this->totalGeneralPayer;
    }

    public function setTotalGeneralPayer(?float $totalGeneralPayer): self
    {
        $this->totalGeneralPayer = $totalGeneralPayer;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?string $modePaiement): self
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function getPieceJointe1(): ?string
    {
        return $this->pieceJointe1;
    }

    public function setPieceJointe1(?string $pieceJointe1): self
    {
        $this->pieceJointe1 = $pieceJointe1;

        return $this;
    }

    public function getPieceJointe2(): ?string
    {
        return $this->pieceJointe2;
    }

    public function setPieceJointe2(?string $pieceJointe2): self
    {
        $this->pieceJointe2 = $pieceJointe2;

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

    public function getDevis(): ?string
    {
        return $this->devis;
    }

    public function setDevis(?string $devis): self
    {
        $this->devis = $devis;

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

    public function getNumeroVehicule(): ?string
    {
        return $this->numeroVehicule;
    }

    public function setNumeroVehicule(?string $numeroVehicule): self
    {
        $this->numeroVehicule = $numeroVehicule;

        return $this;
    }

    public function getSupplementJournalier(): ?float
    {
        return $this->supplementJournalier;
    }

    public function setSupplementJournalier(?float $supplementJournalier): self
    {
        $this->supplementJournalier = $supplementJournalier;

        return $this;
    }

    public function getIndemniteChantier(): ?float
    {
        return $this->indemniteChantier;
    }

    public function setIndemniteChantier(?float $indemniteChantier): self
    {
        $this->indemniteChantier = $indemniteChantier;

        return $this;
    }

    public function getStatutDemandeId(): ?StatutDemande
    {
        return $this->statutDemande_id;
    }

    public function setStatutDemandeId(?StatutDemande $statutDemande_id): self
    {
        $this->statutDemande_id = $statutDemande_id;

        return $this;
    }

    public function getAgenceEmetteurId(): ?Agence
    {
        return $this->agenceEmetteurId;
    }

    public function setAgenceEmetteurId(?Agence $agenceEmetteurId): self
    {
        $this->agenceEmetteurId = $agenceEmetteurId;

        return $this;
    }

    public function getServiceEmetteurId(): ?Service
    {
        return $this->serviceEmetteurId;
    }

    public function setServiceEmetteurId(?Service $serviceEmetteurId): self
    {
        $this->serviceEmetteurId = $serviceEmetteurId;

        return $this;
    }

    /**
     * Get the value of agenceDebiteurId
     */
    public function getAgenceDebiteurId()
    {
        return $this->agenceDebiteurId;
    }

    /**
     * Set the value of agenceDebiteurId
     *
     * @return  self
     */
    public function setAgenceDebiteurId($agenceDebiteurId)
    {
        $this->agenceDebiteurId = $agenceDebiteurId;

        return $this;
    }

    /**
     * Get the value of serviceDebiteurId
     */
    public function getServiceDebiteurId()
    {
        return $this->serviceDebiteurId;
    }

    /**
     * Set the value of serviceDebiteurId
     *
     * @return  self
     */
    public function setServiceDebiteurId($serviceDebiteurId)
    {
        $this->serviceDebiteurId = $serviceDebiteurId;

        return $this;
    }

    public function getEmitterAgence(): ?Agence
    {
        return $this->agenceEmetteurId;
    }

    public function getEmitterService(): ?Service
    {
        return $this->serviceEmetteurId;
    }

    public function getDebtorAgence(): ?Agence
    {
        return $this->agenceDebiteurId;
    }

    public function getDebtorService(): ?Service
    {
        return $this->serviceDebiteurId;
    }
}
