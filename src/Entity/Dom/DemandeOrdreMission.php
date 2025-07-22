<?php

namespace App\Entity\Dom;

use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Admin\PersonnelUser\User;
use App\Repository\Dom\DemandeOrdreMissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Expression;

/**
 * @ORM\Entity(repositoryClass=DemandeOrdreMissionRepository::class)
 * @Assert\Expression(
 *     "this.getDateFinMission() > this.getDateDebutMission()",
 *     message="La date de fin de mission doit être postérieure à la date de début."
 * )
 */
class DemandeOrdreMission
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
}
