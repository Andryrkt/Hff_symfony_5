<?php

namespace App\Entity\Rh\Dom;

use App\Entity\Rh\Dom\Indemnite;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Rh\Dom\RmqRepository;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=RmqRepository::class)
 * @ORM\Table(name="dom_rmq")
 * @ORM\HasLifecycleCallbacks
 */
class Rmq
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Indemnite::class, mappedBy="rmqId")
     */
    private $indemnites;

    public function __construct()
    {
        $this->indemnites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Indemnite>
     */
    public function getIndemnites(): Collection
    {
        return $this->indemnites;
    }

    public function addIndemnite(Indemnite $indemnite): self
    {
        if (!$this->indemnites->contains($indemnite)) {
            $this->indemnites[] = $indemnite;
            $indemnite->setRmqId($this);
        }

        return $this;
    }

    public function removeIndemnite(Indemnite $indemnite): self
    {
        if ($this->indemnites->removeElement($indemnite)) {
            // set the owning side to null (unless already changed)
            if ($indemnite->getRmqId() === $this) {
                $indemnite->setRmqId(null);
            }
        }

        return $this;
    }
}
