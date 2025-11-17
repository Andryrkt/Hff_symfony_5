<?php

namespace App\Entity\Admin\Historisation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Admin\Historisation\TypeOperationRepository;

/**
 * @ORM\Entity(repositoryClass=TypeOperationRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class TypeOperation
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
    private $typeOperation;

    /**
     * @ORM\OneToMany(targetEntity=HistoriqueOperationDocument::class, mappedBy="typeOperation")
     */
    private $historiqueOperationDocuments;

    public function __construct()
    {
        $this->historiqueOperationDocuments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeOperation(): ?string
    {
        return $this->typeOperation;
    }

    public function setTypeOperation(string $typeOperation): self
    {
        $this->typeOperation = $typeOperation;

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueOperationDocument>
     */
    public function getHistoriqueOperationDocuments(): Collection
    {
        return $this->historiqueOperationDocuments;
    }

    public function addHistoriqueOperationDocument(HistoriqueOperationDocument $historiqueOperationDocument): self
    {
        if (!$this->historiqueOperationDocuments->contains($historiqueOperationDocument)) {
            $this->historiqueOperationDocuments[] = $historiqueOperationDocument;
            $historiqueOperationDocument->setTypeOperation($this);
        }

        return $this;
    }

    public function removeHistoriqueOperationDocument(HistoriqueOperationDocument $historiqueOperationDocument): self
    {
        if ($this->historiqueOperationDocuments->removeElement($historiqueOperationDocument)) {
            // set the owning side to null (unless already changed)
            if ($historiqueOperationDocument->getTypeOperation() === $this) {
                $historiqueOperationDocument->setTypeOperation(null);
            }
        }

        return $this;
    }
}
