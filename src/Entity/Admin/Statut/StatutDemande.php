<?php

namespace App\Entity\Admin\Statut;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatutDemandeRepository::class)
 */
class StatutDemande
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
    private $codeApplication;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $codeStatut;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeApplication(): ?string
    {
        return $this->codeApplication;
    }

    public function setCodeApplication(string $codeApplication): self
    {
        $this->codeApplication = $codeApplication;

        return $this;
    }

    public function getCodeStatut(): ?string
    {
        return $this->codeStatut;
    }

    public function setCodeStatut(string $codeStatut): self
    {
        $this->codeStatut = $codeStatut;

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
