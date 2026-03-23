<?php

namespace App\Entity\Hf\Rh\Dom;

use App\Entity\Hf\Rh\Dom\Dom;
use App\Entity\Hf\Rh\Dom\Categorie;
use App\Entity\Hf\Rh\Dom\Indemnite;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\Hf\Rh\Dom\SousTypeDocumentRepository;

/**
 * @ORM\Entity(repositoryClass=SousTypeDocumentRepository::class)
 * @ORM\Table(name="dom_sous_type_document")
 * @ORM\HasLifecycleCallbacks
 */
class SousTypeDocument
{
    use TimestampableTrait;

    public const CODE_MISSION = 'MISSION'; //2
    public const CODE_COMPLEMENT = 'COMPLEMENT'; //3
    public const CODE_MUTATION = 'MUTATION'; //5
    public const CODE_FRAIS_EXCEPTIONNEL = 'FRAIS EXCEPTIONNEL'; //10
    public const CODE_TROP_PERCU = 'TROP PERCU'; //11

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
     * @ORM\OneToMany(targetEntity=Categorie::class, mappedBy="sousTypeDocument")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Indemnite::class, mappedBy="sousTypeDocument")
     */
    private $indemnites;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Hf\Rh\Dom\Dom", mappedBy="sousTypeDocument")
     */
    private $doms;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->indemnites = new ArrayCollection();
        $this->doms = new ArrayCollection();
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
            $category->setSousTypeDocument($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getSousTypeDocument() === $this) {
                $category->setSousTypeDocument(null);
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
            $indemnite->setSousTypeDocument($this);
        }

        return $this;
    }

    public function removeIndemnite(Indemnite $indemnite): self
    {
        if ($this->indemnites->removeElement($indemnite)) {
            // set the owning side to null (unless already changed)
            if ($indemnite->getSousTypeDocument() === $this) {
                $indemnite->setSousTypeDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, \App\Entity\Hf\Rh\Dom\Dom>
     */
    public function getDoms(): Collection
    {
        return $this->doms;
    }

    public function addDom(\App\Entity\Hf\Rh\Dom\Dom $dom): self
    {
        if (!$this->doms->contains($dom)) {
            $this->doms[] = $dom;
            $dom->setSousTypeDocument($this);
        }

        return $this;
    }

    public function removeDom(\App\Entity\Hf\Rh\Dom\Dom $dom): self
    {
        if ($this->doms->removeElement($dom)) {
            // set the owning side to null (unless already changed)
            if ($dom->getSousTypeDocument() === $this) {
                $dom->setSousTypeDocument(null);
            }
        }

        return $this;
    }
}
