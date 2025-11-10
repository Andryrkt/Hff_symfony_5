<?php

namespace App\Entity\Admin\ApplicationGroupe;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Admin\ApplicationGroupe\SequenceAppllicationRepository;

/**
 * @ORM\Entity(repositoryClass=SequenceAppllicationRepository::class)
 * @ORM\HasLifecycleCallbacks 
 */
class SequenceAppllication
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
    private $codeApp;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $anneeMois;

    /**
     * @ORM\Column(type="integer")
     */
    private $dernierNumero;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeApp(): ?string
    {
        return $this->codeApp;
    }

    public function setCodeApp(string $codeApp): self
    {
        $this->codeApp = $codeApp;

        return $this;
    }

    public function getAnneeMois(): ?string
    {
        return $this->anneeMois;
    }

    public function setAnneeMois(string $anneeMois): self
    {
        $this->anneeMois = $anneeMois;

        return $this;
    }

    public function getDernierNumero(): ?int
    {
        return $this->dernierNumero;
    }

    public function setDernierNumero(int $dernierNumero): self
    {
        $this->dernierNumero = $dernierNumero;

        return $this;
    }
}
