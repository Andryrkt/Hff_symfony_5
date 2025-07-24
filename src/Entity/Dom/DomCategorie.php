<?php

namespace App\Entity\Dom;

use App\Repository\Dom\DomCategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use \App\Entity\Dom\DomSousTypeDocument;
use App\Entity\Traits\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass=DomCategorieRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class DomCategorie
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $desctiption;

    /**
     * @ORM\ManyToOne(targetEntity=DomSousTypeDocument::class, inversedBy="domCategories")
     */
    private $domSousTypeDocumentId;

    /**
     * @ORM\OneToMany(targetEntity=DomIndemnite::class, mappedBy="domCategorieId")
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

    public function getDesctiption(): ?string
    {
        return $this->desctiption;
    }

    public function setDesctiption(string $desctiption): self
    {
        $this->desctiption = $desctiption;

        return $this;
    }

    public function getDomSousTypeDocumentId(): ?DomSousTypeDocument
    {
        return $this->domSousTypeDocumentId;
    }

    public function setDomSousTypeDocumentId(?DomSousTypeDocument $domSousTypeDocumentId): self
    {
        $this->domSousTypeDocumentId = $domSousTypeDocumentId;

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
            $domIndemnite->setDomCategorieId($this);
        }

        return $this;
    }

    public function removeDomIndemnite(DomIndemnite $domIndemnite): self
    {
        if ($this->domIndemnites->removeElement($domIndemnite)) {
            // set the owning side to null (unless already changed)
            if ($domIndemnite->getDomCategorieId() === $this) {
                $domIndemnite->setDomCategorieId(null);
            }
        }

        return $this;
    }
}
