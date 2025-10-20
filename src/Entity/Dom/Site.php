<?php

namespace App\Entity\Dom;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Dom\SiteRepository;
use App\Entity\Traits\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass=SiteRepository::class)
 * @ORM\Table(name="dom_site")
 */
class Site
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nomZone;

    /**
     * @ORM\OneToMany(targetEntity=Indemnite::class, mappedBy="siteId")
     */
    private $indemnites;

    public function __construct()
    {
        $this->indemnites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomZone(): ?string
    {
        return $this->nomZone;
    }

    public function setNomZone(?string $nomZone): self
    {
        $this->nomZone = $nomZone;

        return $this;
    }

    /**
     * @return Collection<int, Indemnite>
     */
    public function getIndemnites(): Collection
    {
        return $this->indemnites;
    }

    public function addIndemnite(Indemnite $indemnite): self
    {
        if (!$this->indemnites->contains($indemnite)) {
            $this->indemnites[] = $indemnite;
            $indemnite->setSiteId($this);
        }

        return $this;
    }

    public function removeIndemnite(Indemnite $indemnite): self
    {
        if ($this->indemnites->removeElement($indemnite)) {
            // set the owning side to null (unless already changed)
            if ($indemnite->getSiteId() === $this) {
                $indemnite->setSiteId(null);
            }
        }

        return $this;
    }
}
