<?php

namespace App\Entity\Admin\PersonnelUser;

use App\Entity\Admin\Historisation\TypeDocument;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Admin\ApplicationGroupe\Permission;
use App\Repository\Admin\PersonnelUser\UserAccessRepository;
use Symfony\UX\Turbo\Attribute\Broadcast;

/**
 * @ORM\Entity(repositoryClass=UserAccessRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
#[Broadcast]
class UserAccess
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userAccesses")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="userAccesses")
     */
    private $agence;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="userAccesses")
     */
    private $service;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $allAgence = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $allService = false;

    /**
     * @ORM\ManyToMany(targetEntity=Permission::class, inversedBy="userAccesses")
     */
    private $permissions;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDocument::class, inversedBy="userAccesses")
     * @ORM\JoinColumn(name="type_document_id", referencedColumnName="id")
     */
    private $typeDocument;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
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

    public function getAllAgence()
    {
        return $this->allAgence;
    }

    public function setAllAgence($allAgence): self
    {
        $this->allAgence = $allAgence;

        return $this;
    }

    public function getAllService()
    {
        return $this->allService;
    }

    public function setAllService($allService): self
    {
        $this->allService = $allService;

        return $this;
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        $this->permissions->removeElement($permission);

        return $this;
    }

    public function getTypeDocument(): ?TypeDocument
    {
        return $this->typeDocument;
    }

    public function setTypeDocument(?TypeDocument $typeDocument): self
    {
        $this->typeDocument = $typeDocument;

        return $this;
    }
}
