<?php

namespace App\Entity\Dom;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\DomSousTypeDocumentRepository;
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
}
