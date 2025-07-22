<?php

namespace App\Entity\Dom;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\DomSousTypeDocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DomSousTypeDocumentRepository::class)
 */
class DomSousTypeDocument
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
    private $codeSousType;

    /**
     * @ORM\OneToMany(targetEntity=DemandeOrdreMission::class, mappedBy="domSousTypeDocument")
     */
    private $demandeOrdreMissions;

    public function __construct()
    {
        $this->demandeOrdreMissions = new ArrayCollection();
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
}
