<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ServiceRepository;
use App\Entity\Traits\TimestampableTrait;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Broadcast()
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 */
class Service
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
    private $code;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=AgenceService::class, mappedBy="service")
     */
    private $agenceServices;

    public function __construct()
    {
        $this->agenceServices = new ArrayCollection();
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, AgenceService>
     */
    public function getAgenceServices(): Collection
    {
        return $this->agenceServices;
    }

    public function addAgenceService(AgenceService $agenceService): self
    {
        if (!$this->agenceServices->contains($agenceService)) {
            $this->agenceServices[] = $agenceService;
            $agenceService->setService($this);
        }

        return $this;
    }

    public function removeAgenceService(AgenceService $agenceService): self
    {
        if ($this->agenceServices->removeElement($agenceService)) {
            // set the owning side to null (unless already changed)
            if ($agenceService->getService() === $this) {
                $agenceService->setService(null);
            }
        }

        return $this;
    }
}
