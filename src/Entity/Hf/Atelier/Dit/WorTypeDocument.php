<?php

namespace App\Entity\Hf\Atelier\Dit;

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
}
