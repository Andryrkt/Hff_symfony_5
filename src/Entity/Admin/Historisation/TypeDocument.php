<?php

namespace App\Entity\Admin\Historisation;

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

    public const TYPE_DOCUMENT_DIT_NAME = 'DIT';
    public const TYPE_DOCUMENT_OR_NAME = 'OR';
    public const TYPE_DOCUMENT_FAC_NAME = 'FAC';
    public const TYPE_DOCUMENT_RI_NAME = 'RI';
    public const TYPE_DOCUMENT_TIK_NAME = 'TIK';
    public const TYPE_DOCUMENT_DA_NAME = 'DA';
    public const TYPE_DOCUMENT_DOM_NAME = 'DOM';
    public const TYPE_DOCUMENT_BADM_NAME = 'BADM';
    public const TYPE_DOCUMENT_CAS_NAME = 'CAS';
    public const TYPE_DOCUMENT_CDE_NAME = 'CDE';
    public const TYPE_DOCUMENT_DEV_NAME = 'DEV';
    public const TYPE_DOCUMENT_BC_NAME = 'BC';
    public const TYPE_DOCUMENT_AC_NAME = 'AC';
    public const TYPE_DOCUMENT_SW_NAME = 'SW';
    public const TYPE_DOCUMENT_MUT_NAME = 'MUT';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $typeDocumenet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelleDocument;

    /**
     * @ORM\OneToMany(targetEntity=HistoriqueOperationDocument::class, mappedBy="typeDocument")
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

    public function getTypeDocumenet(): ?string
    {
        return $this->typeDocumenet;
    }

    public function setTypeDocumenet(string $typeDocumenet): self
    {
        $this->typeDocumenet = $typeDocumenet;

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
}
