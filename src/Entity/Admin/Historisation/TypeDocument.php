<?php

namespace App\Entity\Admin\Historisation;

use App\Entity\Admin\PersonnelUser\UserAccess;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Admin\Historisation\TypeDocumentRepository;

/**
 * @ORM\Entity(repositoryClass=TypeDocumentRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class TypeDocument
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $typeDocument;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelleDocument;

    /**
     * @ORM\OneToMany(targetEntity=HistoriqueOperationDocument::class, mappedBy="typeDocument")
     */
    private $historiqueOperationDocuments;

    /**
     * @ORM\OneToMany(targetEntity=UserAccess::class, mappedBy="typeDocument")
     */
    private $userAccesses;

    public function __construct()
    {
        $this->historiqueOperationDocuments = new ArrayCollection();
        $this->userAccesses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeDocument(): ?string
    {
        return $this->typeDocument;
    }

    public function setTypeDocument(string $typeDocument): self
    {
        $this->typeDocument = $typeDocument;

        return $this;
    }

    public function getLibelleDocument(): ?string
    {
        return $this->libelleDocument;
    }

    public function setLibelleDocument(string $libelleDocument): self
    {
        $this->libelleDocument = $libelleDocument;

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
            $historiqueOperationDocument->setTypeDocument($this);
        }

        return $this;
    }

    public function removeHistoriqueOperationDocument(HistoriqueOperationDocument $historiqueOperationDocument): self
    {
        if ($this->historiqueOperationDocuments->removeElement($historiqueOperationDocument)) {
            // set the owning side to null (unless already changed)
            if ($historiqueOperationDocument->getTypeDocument() === $this) {
                $historiqueOperationDocument->setTypeDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserAccess>
     */
    public function getUserAccesses(): Collection
    {
        return $this->userAccesses;
    }

    public function addUserAccess(UserAccess $userAccess): self
    {
        if (!$this->userAccesses->contains($userAccess)) {
            $this->userAccesses[] = $userAccess;
            $userAccess->setTypeDocument($this);
        }

        return $this;
    }

    public function removeUserAccess(UserAccess $userAccess): self
    {
        if ($this->userAccesses->removeElement($userAccess)) {
            // set the owning side to null (unless already changed)
            if ($userAccess->getTypeDocument() === $this) {
                $userAccess->setTypeDocument(null);
            }
        }

        return $this;
    }
}
