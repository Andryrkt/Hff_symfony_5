<?php

namespace App\Entity\Hf\Atelier\Dit;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Hf\Atelier\Dit\WorNiveauUrgenceRepository;

/**
 * @ORM\Entity(repositoryClass=WorNiveauUrgenceRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class WorNiveauUrgence
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $code;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
