<?php

namespace App\Entity\Hf\Atelier\Dit;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\CreatedByTrait;
use App\Entity\Traits\AgenceServiceTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Admin\Statut\StatutDemande;
use App\Contract\Entity\CreatedByInterface;
use App\Contract\Entity\AgenceServiceInterface;
use App\Repository\Hf\Atelier\Dit\DitRepository;

/**
 * @ORM\Entity(repositoryClass=DitRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Dit implements CreatedByInterface, AgenceServiceInterface
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
    private $numeroDit;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $typeReparation;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $reparationRealise;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $interneExterne;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nomClient;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $numeroTelClient;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $mailClient;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateOr;

    /**
     * @ORM\Column(type="date")
     */
    private $datePrevueTravaux;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $demandeDevis;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $avisRecouvrement;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $clientSousContrat;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $objectDemande;

    /**
     * @ORM\Column(type="text")
     */
    private $detailDemande;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $livraisonPartiel;

    /**
     * @ORM\Column(type="integer")
     */
    private $idMateriel;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $numeroClient;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $libelleClient;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $numeroOr;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $numeroDevisRattacher;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $statutDevis;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $sectionAffectee;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $statutOr;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateValidationOr;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $etatFacturation;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $ri;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $numeroMigration;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estAnnuler;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAnnulation;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $numeroDemandeDitAvoir;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $numeroDemandeDitRefacturation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estDitAvoir;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estDitRefacturation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estAtePolTana;

    /**
     * @ORM\Column(type="integer")
     */
    private $heureMachine;

    /**
     * @ORM\Column(type="integer")
     */
    private $kmMachine;

    /**
     * @ORM\ManyToOne(targetEntity=WorTypeDocument::class, inversedBy="dits")
     */
    private $worTypeDocument;

    /**
     * @ORM\ManyToOne(targetEntity=WorNiveauUrgence::class, inversedBy="dits")
     */
    private $worNiveauUrgence;

    /**
     * @ORM\ManyToOne(targetEntity=CategorieAteApp::class, inversedBy="dits")
     */
    private $categorieAteApp;

    /**
     * @ORM\ManyToOne(targetEntity=StatutDemande::class, inversedBy="dits")
     */
    private $statutDemande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDit(): ?string
    {
        return $this->numeroDit;
    }

    public function setNumeroDit(string $numeroDit): self
    {
        $this->numeroDit = $numeroDit;

        return $this;
    }

    public function getTypeReparation(): ?string
    {
        return $this->typeReparation;
    }

    public function setTypeReparation(string $typeReparation): self
    {
        $this->typeReparation = $typeReparation;

        return $this;
    }

    public function getReparationRealise(): ?string
    {
        return $this->reparationRealise;
    }

    public function setReparationRealise(string $reparationRealise): self
    {
        $this->reparationRealise = $reparationRealise;

        return $this;
    }

    public function getInterneExterne(): ?string
    {
        return $this->interneExterne;
    }

    public function setInterneExterne(string $interneExterne): self
    {
        $this->interneExterne = $interneExterne;

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

    public function getNumeroTelClient(): ?string
    {
        return $this->numeroTelClient;
    }

    public function setNumeroTelClient(?string $numeroTelClient): self
    {
        $this->numeroTelClient = $numeroTelClient;

        return $this;
    }

    public function getMailClient(): ?string
    {
        return $this->mailClient;
    }

    public function setMailClient(?string $mailClient): self
    {
        $this->mailClient = $mailClient;

        return $this;
    }

    public function getDateOr(): ?\DateTimeInterface
    {
        return $this->dateOr;
    }

    public function setDateOr(?\DateTimeInterface $dateOr): self
    {
        $this->dateOr = $dateOr;

        return $this;
    }

    public function getDatePrevueTravaux(): ?\DateTimeInterface
    {
        return $this->datePrevueTravaux;
    }

    public function setDatePrevueTravaux(\DateTimeInterface $datePrevueTravaux): self
    {
        $this->datePrevueTravaux = $datePrevueTravaux;

        return $this;
    }

    public function getDemandeDevis(): ?string
    {
        return $this->demandeDevis;
    }

    public function setDemandeDevis(string $demandeDevis): self
    {
        $this->demandeDevis = $demandeDevis;

        return $this;
    }

    public function getAvisRecouvrement(): ?string
    {
        return $this->avisRecouvrement;
    }

    public function setAvisRecouvrement(string $avisRecouvrement): self
    {
        $this->avisRecouvrement = $avisRecouvrement;

        return $this;
    }

    public function getClientSousContrat(): ?string
    {
        return $this->clientSousContrat;
    }

    public function setClientSousContrat(?string $clientSousContrat): self
    {
        $this->clientSousContrat = $clientSousContrat;

        return $this;
    }

    public function getObjectDemande(): ?string
    {
        return $this->objectDemande;
    }

    public function setObjectDemande(string $objectDemande): self
    {
        $this->objectDemande = $objectDemande;

        return $this;
    }

    public function getDetailDemande(): ?string
    {
        return $this->detailDemande;
    }

    public function setDetailDemande(string $detailDemande): self
    {
        $this->detailDemande = $detailDemande;

        return $this;
    }

    public function getLivraisonPartiel(): ?string
    {
        return $this->livraisonPartiel;
    }

    public function setLivraisonPartiel(string $livraisonPartiel): self
    {
        $this->livraisonPartiel = $livraisonPartiel;

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

    public function getNumeroClient(): ?string
    {
        return $this->numeroClient;
    }

    public function setNumeroClient(?string $numeroClient): self
    {
        $this->numeroClient = $numeroClient;

        return $this;
    }

    public function getLibelleClient(): ?string
    {
        return $this->libelleClient;
    }

    public function setLibelleClient(?string $libelleClient): self
    {
        $this->libelleClient = $libelleClient;

        return $this;
    }

    public function getNumeroOr(): ?string
    {
        return $this->numeroOr;
    }

    public function setNumeroOr(?string $numeroOr): self
    {
        $this->numeroOr = $numeroOr;

        return $this;
    }

    public function getNumeroDevisRattacher(): ?string
    {
        return $this->numeroDevisRattacher;
    }

    public function setNumeroDevisRattacher(?string $numeroDevisRattacher): self
    {
        $this->numeroDevisRattacher = $numeroDevisRattacher;

        return $this;
    }

    public function getStatutDevis(): ?string
    {
        return $this->statutDevis;
    }

    public function setStatutDevis(?string $statutDevis): self
    {
        $this->statutDevis = $statutDevis;

        return $this;
    }

    public function getSectionAffectee(): ?string
    {
        return $this->sectionAffectee;
    }

    public function setSectionAffectee(?string $sectionAffectee): self
    {
        $this->sectionAffectee = $sectionAffectee;

        return $this;
    }

    public function getStatutOr(): ?string
    {
        return $this->statutOr;
    }

    public function setStatutOr(?string $statutOr): self
    {
        $this->statutOr = $statutOr;

        return $this;
    }

    public function getDateValidationOr(): ?\DateTimeInterface
    {
        return $this->dateValidationOr;
    }

    public function setDateValidationOr(?\DateTimeInterface $dateValidationOr): self
    {
        $this->dateValidationOr = $dateValidationOr;

        return $this;
    }

    public function getEtatFacturation(): ?string
    {
        return $this->etatFacturation;
    }

    public function setEtatFacturation(?string $etatFacturation): self
    {
        $this->etatFacturation = $etatFacturation;

        return $this;
    }

    public function getRi(): ?string
    {
        return $this->ri;
    }

    public function setRi(?string $ri): self
    {
        $this->ri = $ri;

        return $this;
    }

    public function getNumeroMigration(): ?int
    {
        return $this->numeroMigration;
    }

    public function setNumeroMigration(?int $numeroMigration): self
    {
        $this->numeroMigration = $numeroMigration;

        return $this;
    }

    public function isEstAnnuler(): ?bool
    {
        return $this->estAnnuler;
    }

    public function setEstAnnuler(bool $estAnnuler): self
    {
        $this->estAnnuler = $estAnnuler;

        return $this;
    }

    public function getDateAnnulation(): ?\DateTimeInterface
    {
        return $this->dateAnnulation;
    }

    public function setDateAnnulation(?\DateTimeInterface $dateAnnulation): self
    {
        $this->dateAnnulation = $dateAnnulation;

        return $this;
    }

    public function getNumeroDemandeDitAvoir(): ?string
    {
        return $this->numeroDemandeDitAvoir;
    }

    public function setNumeroDemandeDitAvoir(?string $numeroDemandeDitAvoir): self
    {
        $this->numeroDemandeDitAvoir = $numeroDemandeDitAvoir;

        return $this;
    }

    public function getNumeroDemandeDitRefacturation(): ?string
    {
        return $this->numeroDemandeDitRefacturation;
    }

    public function setNumeroDemandeDitRefacturation(string $numeroDemandeDitRefacturation): self
    {
        $this->numeroDemandeDitRefacturation = $numeroDemandeDitRefacturation;

        return $this;
    }

    public function isEstDitAvoir(): ?bool
    {
        return $this->estDitAvoir;
    }

    public function setEstDitAvoir(bool $estDitAvoir): self
    {
        $this->estDitAvoir = $estDitAvoir;

        return $this;
    }

    public function isEstDitRefacturation(): ?bool
    {
        return $this->estDitRefacturation;
    }

    public function setEstDitRefacturation(bool $estDitRefacturation): self
    {
        $this->estDitRefacturation = $estDitRefacturation;

        return $this;
    }

    public function isEstAtePolTana(): ?bool
    {
        return $this->estAtePolTana;
    }

    public function setEstAtePolTana(bool $estAtePolTana): self
    {
        $this->estAtePolTana = $estAtePolTana;

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

    public function getWorTypeDocument(): ?WorTypeDocument
    {
        return $this->worTypeDocument;
    }

    public function setWorTypeDocument(?WorTypeDocument $worTypeDocument): self
    {
        $this->worTypeDocument = $worTypeDocument;

        return $this;
    }

    public function getWorNiveauUrgence(): ?WorNiveauUrgence
    {
        return $this->worNiveauUrgence;
    }

    public function setWorNiveauUrgence(?WorNiveauUrgence $worNiveauUrgence): self
    {
        $this->worNiveauUrgence = $worNiveauUrgence;

        return $this;
    }

    public function getCategorieAteApp(): ?CategorieAteApp
    {
        return $this->categorieAteApp;
    }

    public function setCategorieAteApp(?CategorieAteApp $categorieAteApp): self
    {
        $this->categorieAteApp = $categorieAteApp;

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
