<?php

namespace App\Entity\Admin\ApplicationGroupe;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\Admin\ApplicationGroupe\VignetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VignetteRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Vignette
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Permission::class, mappedBy="vignette")
     */
    private $permission;

    public function __construct()
    {
        $this->permission = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, Permission>
     */
    public function getPermission(): Collection
    {
        return $this->permission;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permission->contains($permission)) {
            $this->permission[] = $permission;
            $permission->setVignette($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permission->removeElement($permission)) {
            // set the owning side to null (unless already changed)
            if ($permission->getVignette() === $this) {
                $permission->setVignette(null);
            }
        }

        return $this;
    }
}
