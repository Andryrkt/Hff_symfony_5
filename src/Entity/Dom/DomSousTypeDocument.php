<?php

namespace App\Entity\Dom;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\Dom\DomSousTypeDocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DomSousTypeDocumentRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class DomSousTypeDocument
{
    use TimestampableTrait;

    public const CODE_SOUS_TYPE_MUTATION = 'MUTATION';
    public const CODE_SOUS_TYPE_TROP_PERCU = 'TROP PERCU';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $codeSousType;

    /**
     * @ORM\OneToMany(targetEntity=DemandeOrdreMission::class, mappedBy="domSousTypeDocument")
     */
    private $demandeOrdreMissions;

    /**
     * @ORM\OneToMany(targetEntity=DomCategorie::class, mappedBy="domSousTypeDocumentId")
     */
    private $domCategories;

    /**
     * @ORM\OneToMany(targetEntity=DomIndemnite::class, mappedBy="domSousTypeDocumentId")
     */
    private $domIndemnites;

    public function __construct()
    {
        $this->demandeOrdreMissions = new ArrayCollection();
        $this->domCategories = new ArrayCollection();
        $this->domIndemnites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeSousType(): ?string
    {
        return $this->codeSousType;
    }

    public function setCodeSousType(string $codeSousType): self
    {
        $this->codeSousType = $codeSousType;

        return $this;
    }

    /**
     * @return Collection<int, DemandeOrdreMission>
     */
    public function getDemandeOrdreMissions(): Collection
    {
        return $this->demandeOrdreMissions;
    }

    public function addDemandeOrdreMission(DemandeOrdreMission $demandeOrdreMission): self
    {
        if (!$this->demandeOrdreMissions->contains($demandeOrdreMission)) {
            $this->demandeOrdreMissions[] = $demandeOrdreMission;
            $demandeOrdreMission->setDomSousTypeDocument($this);
        }

        return $this;
    }

    public function removeDemandeOrdreMission(DemandeOrdreMission $demandeOrdreMission): self
    {
        if ($this->demandeOrdreMissions->removeElement($demandeOrdreMission)) {
            // set the owning side to null (unless already changed)
            if ($demandeOrdreMission->getDomSousTypeDocument() === $this) {
                $demandeOrdreMission->setDomSousTypeDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DomCategorie>
     */
    public function getDomCategories(): Collection
    {
        return $this->domCategories;
    }

    public function addDomCategory(DomCategorie $domCategory): self
    {
        if (!$this->domCategories->contains($domCategory)) {
            $this->domCategories[] = $domCategory;
            $domCategory->setDomSousTypeDocumentId($this);
        }

        return $this;
    }

    public function removeDomCategory(DomCategorie $domCategory): self
    {
        if ($this->domCategories->removeElement($domCategory)) {
            // set the owning side to null (unless already changed)
            if ($domCategory->getDomSousTypeDocumentId() === $this) {
                $domCategory->setDomSousTypeDocumentId(null);
            }
        }

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
            $domIndemnite->setDomSousTypeDocumentId($this);
        }

        return $this;
    }

    public function removeDomIndemnite(DomIndemnite $domIndemnite): self
    {
        if ($this->domIndemnites->removeElement($domIndemnite)) {
            // set the owning side to null (unless already changed)
            if ($domIndemnite->getDomSousTypeDocumentId() === $this) {
                $domIndemnite->setDomSousTypeDocumentId(null);
            }
        }

        return $this;
    }
}
