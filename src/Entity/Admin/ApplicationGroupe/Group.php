<?php

namespace App\Entity\Admin\ApplicationGroupe;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Admin\PersonnelUser\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Admin\ApplicationGroupe\GroupAccess;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_group")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="groups")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=GroupAccess::class, mappedBy="group", cascade={"persist", "remove"})
     */
    private $groupAccesses;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->groupAccesses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addGroup($this);
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeGroup($this);
        }
        return $this;
    }

    public function getGroupAccesses(): Collection
    {
        return $this->groupAccesses;
    }
}
