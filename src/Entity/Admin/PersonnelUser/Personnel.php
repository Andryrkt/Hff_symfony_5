<?php

namespace App\Entity\Admin\PersonnelUser;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Admin\PersonnelUser\PersonnelRepository;
use App\Entity\Traits\TimestampableTrait;
use Symfony\UX\Turbo\Attribute\Broadcast;
use App\Entity\Admin\AgenceService\AgenceService;
use App\Entity\Admin\PersonnelUser\User;

/**
 * @Broadcast()
 * @ORM\Entity(repositoryClass=PersonnelRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Personnel
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\ManyToOne(targetEntity=AgenceService::class, inversedBy="personnels")
     */
    private $agenceService;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="personnel", cascade={"persist", "remove"})
     */
    private $users;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAgenceService(): ?AgenceService
    {
        return $this->agenceService;
    }

    public function setAgenceService(?AgenceService $agenceService): self
    {
        $this->agenceService = $agenceService;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        // unset the owning side of the relation if necessary
        if ($users === null && $this->users !== null) {
            $this->users->setPersonnel(null);
        }

        // set the owning side of the relation if necessary
        if ($users !== null && $users->getPersonnel() !== $this) {
            $users->setPersonnel($this);
        }

        $this->users = $users;

        return $this;
    }
}
