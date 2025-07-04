<?php

namespace App\Entity\Admin\AgenceService;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Admin\AgenceService\ServiceRepository;
use App\Entity\Traits\TimestampableTrait;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Entity\Admin\AgenceService\AgenceServiceIrium;

/**
 * @Broadcast()
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 * @ORM\HasLifecycleCallbacks
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
     * @ORM\OneToMany(targetEntity=AgenceServiceIrium::class, mappedBy="service")
     */
    private $agenceServiceIriums;

    /**
     * @ORM\OneToMany(targetEntity=UserAccess::class, mappedBy="service")
     */
    private $userAccesses;

    public function __construct()
    {
        $this->agenceServiceIriums = new ArrayCollection();
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
     * @return Collection<int, AgenceServiceIrium>
     */
    public function getAgenceServiceIriums(): Collection
    {
        return $this->agenceServiceIriums;
    }

    public function addAgenceServiceIrium(AgenceServiceIrium $agenceServiceIrium): self
    {
        if (!$this->agenceServiceIriums->contains($agenceServiceIrium)) {
            $this->agenceServiceIriums[] = $agenceServiceIrium;
            $agenceServiceIrium->setService($this);
        }

        return $this;
    }

    public function removeAgenceServiceIrium(AgenceServiceIrium $agenceServiceIrium): self
    {
        if ($this->agenceServiceIriums->removeElement($agenceServiceIrium)) {
            // set the owning side to null (unless already changed)
            if ($agenceServiceIrium->getService() === $this) {
                $agenceServiceIrium->setService(null);
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
            $userAccess->setService($this);
        }

        return $this;
    }

    public function removeUserAccess(UserAccess $userAccess): self
    {
        if ($this->userAccesses->removeElement($userAccess)) {
            // set the owning side to null (unless already changed)
            if ($userAccess->getService() === $this) {
                $userAccess->setService(null);
            }
        }

        return $this;
    }
}
