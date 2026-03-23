<?php

namespace App\Entity\Hf\Materiel\Badm;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Hf\Materiel\Badm\TypeMouvementRepository;

/**
 * @ORM\Entity(repositoryClass=TypeMouvementRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class TypeMouvement
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
    private $codeMouvement;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Badm::class, mappedBy="typeMouvement")
     */
    private $badms;

    public function __construct()
    {
        $this->badms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeMouvement(): ?string
    {
        return $this->codeMouvement;
    }

    public function setCodeMouvement(string $codeMouvement): self
    {
        $this->codeMouvement = $codeMouvement;

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

    /**
     * @return Collection<int, Badm>
     */
    public function getBadms(): Collection
    {
        return $this->badms;
    }

    public function addBadm(Badm $badm): self
    {
        if (!$this->badms->contains($badm)) {
            $this->badms[] = $badm;
            $badm->setTypeMouvement($this);
        }

        return $this;
    }

    public function removeBadm(Badm $badm): self
    {
        if ($this->badms->removeElement($badm)) {
            // set the owning side to null (unless already changed)
            if ($badm->getTypeMouvement() === $this) {
                $badm->setTypeMouvement(null);
            }
        }

        return $this;
    }
}
