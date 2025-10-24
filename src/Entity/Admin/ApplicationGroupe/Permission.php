<?php

namespace App\Entity\Admin\ApplicationGroupe;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\Admin\ApplicationGroupe\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PermissionRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Permission
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Vignette::class, inversedBy="permission")
     */
    private $vignette;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getVignette(): ?Vignette
    {
        return $this->vignette;
    }

    public function setVignette(?Vignette $vignette): self
    {
        $this->vignette = $vignette;

        return $this;
    }
}
