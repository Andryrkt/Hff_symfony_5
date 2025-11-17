<?php

namespace App\Entity\Admin\Historisation;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Admin\Historisation\HistoriqueOperationDocumentRepository;

/**
 * @ORM\Entity(repositoryClass=HistoriqueOperationDocumentRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class HistoriqueOperationDocument
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
    private $numeroDocument;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $utilisateur;

    /**
     * @ORM\ManyToOne(targetEntity=TypeOperation::class, inversedBy="historiqueOperationDocuments")
     */
    private $typeOperation;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDocument::class, inversedBy="historiqueOperationDocuments")
     */
    private $typeDocument;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDocument(): ?string
    {
        return $this->numeroDocument;
    }

    public function setNumeroDocument(?string $numeroDocument): self
    {
        $this->numeroDocument = $numeroDocument;

        return $this;
    }

    public function getUtilisateur(): ?string
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?string $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getTypeOperation(): ?TypeOperation
    {
        return $this->typeOperation;
    }

    public function setTypeOperation(?TypeOperation $typeOperation): self
    {
        $this->typeOperation = $typeOperation;

        return $this;
    }

    public function getTypeDocument(): ?TypeDocument
    {
        return $this->typeDocument;
    }

    public function setTypeDocument(?TypeDocument $typeDocument): self
    {
        $this->typeDocument = $typeDocument;

        return $this;
    }
}
