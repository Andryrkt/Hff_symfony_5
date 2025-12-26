<?php

namespace App\Entity\Hf\Atelier\Dit;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Hf\Atelier\Dit\WorTypeDocumentRepository;

/**
 * @ORM\Entity(repositoryClass=WorTypeDocumentRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class WorTypeDocument
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $codeDocument;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Dit::class, mappedBy="worTypeDocument")
     */
    private $dits;

    public function __construct()
    {
        $this->dits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeDocument(): ?string
    {
        return $this->codeDocument;
    }

    public function setCodeDocument(string $codeDocument): self
    {
        $this->codeDocument = $codeDocument;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Dit>
     */
    public function getDits(): Collection
    {
        return $this->dits;
    }

    public function addDit(Dit $dit): self
    {
        if (!$this->dits->contains($dit)) {
            $this->dits[] = $dit;
            $dit->setWorTypeDocument($this);
        }

        return $this;
    }

    public function removeDit(Dit $dit): self
    {
        if ($this->dits->removeElement($dit)) {
            // set the owning side to null (unless already changed)
            if ($dit->getWorTypeDocument() === $this) {
                $dit->setWorTypeDocument(null);
            }
        }

        return $this;
    }
}
