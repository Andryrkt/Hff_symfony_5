<?php

namespace App\Entity\Dom;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\Dom\DomRmqRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DomRmqRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class DomRmq
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=DomIndemnite::class, mappedBy="domRmqId")
     */
    private $domIndemnites;

    public function __construct()
    {
        $this->domIndemnites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, DomIndemnite>
     */
    public function getDomIndemnites(): Collection
    {
        return $this->domIndemnites;
    }

    public function addDomIndemnite(DomIndemnite $domIndemnite): self
    {
        if (!$this->domIndemnites->contains($domIndemnite)) {
            $this->domIndemnites[] = $domIndemnite;
            $domIndemnite->setDomRmqId($this);
        }

        return $this;
    }

    public function removeDomIndemnite(DomIndemnite $domIndemnite): self
    {
        if ($this->domIndemnites->removeElement($domIndemnite)) {
            // set the owning side to null (unless already changed)
            if ($domIndemnite->getDomRmqId() === $this) {
                $domIndemnite->setDomRmqId(null);
            }
        }

        return $this;
    }
}
