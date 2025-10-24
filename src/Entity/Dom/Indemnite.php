<?php

namespace App\Entity\Dom;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Dom\IndemniteRepository;

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
    private $siteId;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="indemnites")
     */
    private $categorieId;

    /**
     * @ORM\ManyToOne(targetEntity=Rmq::class, inversedBy="indemnites")
     */
    private $rmqId;

    /**
     * @ORM\ManyToOne(targetEntity=SousTypeDocument::class, inversedBy="indemnites")
     */
    private $sousTypeDocumentId;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montant;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCategorieId(): ?Categorie
    {
        return $this->categorieId;
    }

    public function setCategorieId(?Categorie $categorieId): self
    {
        $this->categorieId = $categorieId;

        return $this;
    }

    public function getRmqId(): ?Rmq
    {
        return $this->rmqId;
    }

    public function setRmqId(?Rmq $rmqId): self
    {
        $this->rmqId = $rmqId;

        return $this;
    }

    public function getSousTypeDocumentId(): ?SousTypeDocument
    {
        return $this->sousTypeDocumentId;
    }

    public function setSousTypeDocumentId(?SousTypeDocument $sousTypeDocumentId): self
    {
        $this->sousTypeDocumentId = $sousTypeDocumentId;

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
