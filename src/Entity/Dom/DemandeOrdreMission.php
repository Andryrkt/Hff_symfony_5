<?php

namespace App\Entity\Dom;

use App\Repository\Dom\DemandeOrdreMissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DemandeOrdreMissionRepository::class)
 */
class DemandeOrdreMission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $numeroOrdreMission;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroOrdreMission(): ?string
    {
        return $this->numeroOrdreMission;
    }

    public function setNumeroOrdreMission(string $numeroOrdreMission): self
    {
        $this->numeroOrdreMission = $numeroOrdreMission;

        return $this;
    }
}
