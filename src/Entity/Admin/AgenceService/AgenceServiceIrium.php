<?php

namespace App\Entity\Admin\AgenceService;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Symfony\UX\Turbo\Attribute\Broadcast;
use App\Repository\Admin\AgenceService\AgenceServiceIriumRepository;
use Doctrine\Common\Collections\Collection;
use App\Entity\Admin\PersonnelUser\Personnel;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;

/**
 * @Broadcast()
 * @ORM\Entity(repositoryClass=AgenceServiceIriumRepository::class)
 * @ORM\Table(name="agence_service_irium")
 * @ORM\HasLifecycleCallbacks
 */
class AgenceServiceIrium
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="agenceServiceIriums")
     */
    private $agence;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="agenceServiceIriums")
     */
    private $service;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $responsable;

    /**
     * @ORM\OneToMany(targetEntity=Personnel::class, mappedBy="agenceServiceIrium")
     */
    private $personnels;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $societe;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $codeSage;

    public function __construct()
    {
        $this->personnels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getResponsable(): ?string
    {
        return $this->responsable;
    }

    public function setResponsable(?string $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * @return Collection<int, Personnel>
     */
    public function getPersonnels(): Collection
    {
        return $this->personnels;
    }

    public function addPersonnel(Personnel $personnel): self
    {
        if (!$this->personnels->contains($personnel)) {
            $this->personnels[] = $personnel;
            $personnel->setAgenceServiceIrium($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getAgenceServiceIrium() === $this) {
                $personnel->setAgenceServiceIrium(null);
            }
        }

        return $this;
    }

    public function getSociete(): ?string
    {
        return $this->societe;
    }

    public function setSociete(?string $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    public function getCodeSage(): ?string
    {
        return $this->codeSage;
    }

    public function setCodeSage(?string $codeSage): self
    {
        $this->codeSage = $codeSage;

        return $this;
    }
}
