<?php

namespace App\Entity\Dom;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\Dom\DomSiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DomSiteRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class DomSite
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nomZone;

    /**
     * @ORM\OneToMany(targetEntity=DomIndemnite::class, mappedBy="domSiteId")
     */
    private $domIndemnites;

    public function __construct()
    {
        $this->domIndemnites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomZone(): ?string
    {
        return $this->nomZone;
    }

    public function setNomZone(string $nomZone): self
    {
        $this->nomZone = $nomZone;

        return $this;
    }

    /**
     * @return Collection<int, DomIndemnite>
     */
    public function getDomIndemnites(): Collection
    {
        return $this->domIndemnites;
    }

    public function addDomIndemnite(DomIndemnite $domIndemnite): self
    {
        if (!$this->domIndemnites->contains($domIndemnite)) {
            $this->domIndemnites[] = $domIndemnite;
            $domIndemnite->setDomSiteId($this);
        }

        return $this;
    }

    public function removeDomIndemnite(DomIndemnite $domIndemnite): self
    {
        if ($this->domIndemnites->removeElement($domIndemnite)) {
            // set the owning side to null (unless already changed)
            if ($domIndemnite->getDomSiteId() === $this) {
                $domIndemnite->setDomSiteId(null);
            }
        }

        return $this;
    }
}
