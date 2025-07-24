<?php

namespace App\Entity\Dom;

use App\Repository\Dom\DomIndemniteRepository;
use Doctrine\ORM\Mapping as ORM;
use \App\Entity\Dom\DomSite;
use \App\Entity\Dom\DomCategorie;
use \App\Entity\Dom\DomRmq;
use App\Entity\Traits\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass=DomIndemniteRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class DomIndemnite
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity=DomSite::class, inversedBy="domIndemnites")
     */
    private $domSiteId;

    /**
     * @ORM\ManyToOne(targetEntity=DomCategorie::class, inversedBy="domIndemnites")
     */
    private $domCategorieId;

    /**
     * @ORM\ManyToOne(targetEntity=DomRmq::class, inversedBy="domIndemnites")
     */
    private $domRmqId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDomSiteId(): ?DomSite
    {
        return $this->domSiteId;
    }

    public function setDomSiteId(?DomSite $domSiteId): self
    {
        $this->domSiteId = $domSiteId;

        return $this;
    }

    public function getDomCategorieId(): ?DomCategorie
    {
        return $this->domCategorieId;
    }

    public function setDomCategorieId(?DomCategorie $domCategorieId): self
    {
        $this->domCategorieId = $domCategorieId;

        return $this;
    }

    public function getDomRmqId(): ?DomRmq
    {
        return $this->domRmqId;
    }

    public function setDomRmqId(?DomRmq $domRmqId): self
    {
        $this->domRmqId = $domRmqId;

        return $this;
    }
}
