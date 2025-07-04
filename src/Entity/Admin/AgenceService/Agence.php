<?php

namespace App\Entity\Admin\AgenceService;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Admin\AgenceService\AgenceRepository;
use App\Entity\Traits\TimestampableTrait;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Entity\Admin\AgenceService\AgenceService;

/**
 * @Broadcast()
 * @ORM\Entity(repositoryClass=AgenceRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Agence
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=AgenceService::class, mappedBy="agence")
     */
    private $agenceServices;

    /**
     * @ORM\OneToMany(targetEntity=UserAccess::class, mappedBy="agence")
     */
    private $userAccesses;

    public function __construct()
    {
        $this->agenceServices = new ArrayCollection();
        $this->userAccesses = new ArrayCollection();
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
            $agenceService->setAgence($this);
        }

        return $this;
    }

    public function removeAgenceService(AgenceService $agenceService): self
    {
        if ($this->agenceServices->removeElement($agenceService)) {
            // set the owning side to null (unless already changed)
            if ($agenceService->getAgence() === $this) {
                $agenceService->setAgence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserAccess>
     */
    public function getUserAccesses(): Collection
    {
        return $this->userAccesses;
    }

    public function addUserAccess(UserAccess $userAccess): self
    {
        if (!$this->userAccesses->contains($userAccess)) {
            $this->userAccesses[] = $userAccess;
            $userAccess->setAgence($this);
        }

        return $this;
    }

    public function removeUserAccess(UserAccess $userAccess): self
    {
        if ($this->userAccesses->removeElement($userAccess)) {
            // set the owning side to null (unless already changed)
            if ($userAccess->getAgence() === $this) {
                $userAccess->setAgence(null);
            }
        }

        return $this;
    }
}
