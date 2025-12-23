<?php

namespace App\Entity\Hf\Atelier\Dit\Ors;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\CreatedByTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Contract\Entity\CreatedByInterface;
use App\Repository\Hf\Atelier\Dit\Ors\OrsRepository;

/**
 * @ORM\Entity(repositoryClass=OrsRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Ors implements CreatedByInterface
{
    use TimestampableTrait;
    use CreatedByTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $numeroDit;

    /**
     * @ORM\Column(type="integer")
     */
    private $numeroOr;

    /**
     * @ORM\Column(type="smallint")
     */
    private $numeroItv;

    /**
     * @ORM\Column(type="smallint")
     */
    private $nombreLigneItv;

    /**
     * @ORM\Column(type="float")
     */
    private $montantItv;

    /**
     * @ORM\Column(type="float")
     */
    private $montantPiece;

    /**
     * @ORM\Column(type="float")
     */
    private $montantMo;

    /**
     * @ORM\Column(type="float")
     */
    private $montantAchatLocaux;

    /**
     * @ORM\Column(type="float")
     */
    private $montantFraisDivers;

    /**
     * @ORM\Column(type="float")
     */
    private $montantLubrifiants;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $libellelItv;

    /**
     * @ORM\Column(type="string", length=3000, nullable=true)
     */
    private $observation;

    /**
     * @ORM\Column(type="smallint")
     */
    private $numeroVersion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $migration;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pieceFaibleActiviteAchat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDit(): ?string
    {
        return $this->numeroDit;
    }

    public function setNumeroDit(?string $numeroDit): self
    {
        $this->numeroDit = $numeroDit;

        return $this;
    }

    public function getNumeroOr(): ?int
    {
        return $this->numeroOr;
    }

    public function setNumeroOr(int $numeroOr): self
    {
        $this->numeroOr = $numeroOr;

        return $this;
    }

    public function getNumeroItv(): ?int
    {
        return $this->numeroItv;
    }

    public function setNumeroItv(int $numeroItv): self
    {
        $this->numeroItv = $numeroItv;

        return $this;
    }

    public function getNombreLigneItv(): ?int
    {
        return $this->nombreLigneItv;
    }

    public function setNombreLigneItv(int $nombreLigneItv): self
    {
        $this->nombreLigneItv = $nombreLigneItv;

        return $this;
    }

    public function getMontantItv(): ?float
    {
        return $this->montantItv;
    }

    public function setMontantItv(float $montantItv): self
    {
        $this->montantItv = $montantItv;

        return $this;
    }

    public function getMontantPiece(): ?float
    {
        return $this->montantPiece;
    }

    public function setMontantPiece(float $montantPiece): self
    {
        $this->montantPiece = $montantPiece;

        return $this;
    }

    public function getMontantMo(): ?float
    {
        return $this->montantMo;
    }

    public function setMontantMo(float $montantMo): self
    {
        $this->montantMo = $montantMo;

        return $this;
    }

    public function getMontantAchatLocaux(): ?float
    {
        return $this->montantAchatLocaux;
    }

    public function setMontantAchatLocaux(float $montantAchatLocaux): self
    {
        $this->montantAchatLocaux = $montantAchatLocaux;

        return $this;
    }

    public function getMontantFraisDivers(): ?float
    {
        return $this->montantFraisDivers;
    }

    public function setMontantFraisDivers(float $montantFraisDivers): self
    {
        $this->montantFraisDivers = $montantFraisDivers;

        return $this;
    }

    public function getMontantLubrifiants(): ?float
    {
        return $this->montantLubrifiants;
    }

    public function setMontantLubrifiants(float $montantLubrifiants): self
    {
        $this->montantLubrifiants = $montantLubrifiants;

        return $this;
    }

    public function getLibellelItv(): ?string
    {
        return $this->libellelItv;
    }

    public function setLibellelItv(?string $libellelItv): self
    {
        $this->libellelItv = $libellelItv;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }

    public function getNumeroVersion(): ?int
    {
        return $this->numeroVersion;
    }

    public function setNumeroVersion(int $numeroVersion): self
    {
        $this->numeroVersion = $numeroVersion;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMigration(): ?int
    {
        return $this->migration;
    }

    public function setMigration(?int $migration): self
    {
        $this->migration = $migration;

        return $this;
    }

    public function isPieceFaibleActiviteAchat(): ?bool
    {
        return $this->pieceFaibleActiviteAchat;
    }

    public function setPieceFaibleActiviteAchat(?bool $pieceFaibleActiviteAchat): self
    {
        $this->pieceFaibleActiviteAchat = $pieceFaibleActiviteAchat;

        return $this;
    }
}
