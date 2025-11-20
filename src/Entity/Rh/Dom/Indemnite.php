<?php

namespace App\Entity\Rh\Dom;

use App\Entity\Rh\Dom\Rmq;
use App\Entity\Rh\Dom\Site;
use App\Entity\Rh\Dom\Categorie;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Rh\Dom\SousTypeDocument;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Rh\Dom\IndemniteRepository;


/**
 * @ORM\Entity(repositoryClass=IndemniteRepository::class)
 * @ORM\Table(name="dom_indemnite")
 * @ORM\HasLifecycleCallbacks
 */
class Indemnite
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="indemnites")
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="indemnites")
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity=Rmq::class, inversedBy="indemnites")
     */
    private $rmq;

    /**
     * @ORM\ManyToOne(targetEntity=SousTypeDocument::class, inversedBy="indemnites")
     */
    private $sousTypeDocument;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getRmq(): ?Rmq
    {
        return $this->rmq;
    }

    public function setRmq(?Rmq $rmq): self
    {
        $this->rmq = $rmq;

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

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }
}
