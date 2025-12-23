<?php

namespace App\Entity\Hf\Atelier\Dit;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity=Dit::class, mappedBy="worNiveauUrgence")
     */
    private $dits;

    public function __construct()
    {
        $this->dits = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Dit>
     */
    public function getDits(): Collection
    {
        return $this->dits;
    }

    public function addDit(Dit $dit): self
    {
        if (!$this->dits->contains($dit)) {
            $this->dits[] = $dit;
            $dit->setWorNiveauUrgence($this);
        }

        return $this;
    }

    public function removeDit(Dit $dit): self
    {
        if ($this->dits->removeElement($dit)) {
            // set the owning side to null (unless already changed)
            if ($dit->getWorNiveauUrgence() === $this) {
                $dit->setWorNiveauUrgence(null);
            }
        }

        return $this;
    }
}
