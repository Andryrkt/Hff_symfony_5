<?php

namespace App\Entity\Dom;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\Dom\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 * @ORM\Table(name="dom_categorie")
 */
class Categorie
{
    use TimestampableTrait;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SousTypeDocument::class, inversedBy="categories")
     */
    private $sousTypeDocumentId;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Indemnite::class, mappedBy="CategorieId")
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

    public function getSousTypeDocumentId(): ?SousTypeDocument
    {
        return $this->sousTypeDocumentId;
    }

    public function setSousTypeDocumentId(?SousTypeDocument $sousTypeDocumentId): self
    {
        $this->sousTypeDocumentId = $sousTypeDocumentId;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
            $indemnite->setCategorieId($this);
        }

        return $this;
    }

    public function removeIndemnite(Indemnite $indemnite): self
    {
        if ($this->indemnites->removeElement($indemnite)) {
            // set the owning side to null (unless already changed)
            if ($indemnite->getCategorieId() === $this) {
                $indemnite->setCategorieId(null);
            }
        }

        return $this;
    }
}
