<?php

namespace App\Entity\Dom;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\Dom\SousTypeDocumentRepository;

/**
 * @ORM\Entity(repositoryClass=SousTypeDocumentRepository::class)
 * @ORM\Table(name="dom_sous_type_document")
 * @ORM\HasLifecycleCallbacks
 */
class SousTypeDocument
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
    private $codeSousType;

    /**
     * @ORM\OneToMany(targetEntity=Categorie::class, mappedBy="sousTypeDocumentId")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Indemnite::class, mappedBy="sousTypeDocumentId")
     */
    private $indemnites;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->indemnites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeSousType(): ?string
    {
        return $this->codeSousType;
    }

    public function setCodeSousType(?string $codeSousType): self
    {
        $this->codeSousType = $codeSousType;

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setSousTypeDocumentId($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getSousTypeDocumentId() === $this) {
                $category->setSousTypeDocumentId(null);
            }
        }

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
            $indemnite->setSousTypeDocumentId($this);
        }

        return $this;
    }

    public function removeIndemnite(Indemnite $indemnite): self
    {
        if ($this->indemnites->removeElement($indemnite)) {
            // set the owning side to null (unless already changed)
            if ($indemnite->getSousTypeDocumentId() === $this) {
                $indemnite->setSousTypeDocumentId(null);
            }
        }

        return $this;
    }
}
