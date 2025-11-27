<?php

namespace App\Entity\Admin\PersonnelUser;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Admin\PersonnelUser\PersonnelRepository;
use App\Entity\Traits\TimestampableTrait;
use Symfony\UX\Turbo\Attribute\Broadcast;
use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use App\Entity\Admin\PersonnelUser\User;

/**
 * @Broadcast()
 * @ORM\Entity(repositoryClass=PersonnelRepository::class)
 * @ORM\Table(name="personnel",
 * indexes={
 *         @ORM\Index(name="idx_matricule", columns={"matricule"})
 *     }
 * )
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
    private $prenoms;

    /**
     * @ORM\ManyToOne(targetEntity=AgenceServiceIrium::class, inversedBy="personnels")
     * @ORM\JoinColumn(name="agence_service_irium_id", referencedColumnName="id")
     */
    private $agenceServiceIrium;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="personnel", cascade={"persist", "remove"})
     */
    private $users;


    /**
     * @ORM\Column(type="integer", nullable=true, unique=true)
     */
    private $matricule;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $societe;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $numeroCompteBancaire;

    public function __construct() {}

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

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): self
    {
        $this->prenoms = $prenoms;

        return $this;
    }

    public function getAgenceServiceIrium(): ?AgenceServiceIrium
    {
        return $this->agenceServiceIrium;
    }

    public function setAgenceServiceIrium(?AgenceServiceIrium $agenceServiceIrium): self
    {
        $this->agenceServiceIrium = $agenceServiceIrium;

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


    public function getMatricule(): ?int
    {
        return $this->matricule;
    }

    public function setMatricule(?int $matricule): self
    {
        $this->matricule = $matricule;

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

    public function getNumeroCompteBancaire(): ?string
    {
        return $this->numeroCompteBancaire;
    }

    public function setNumeroCompteBancaire(?string $numeroCompteBancaire): self
    {
        $this->numeroCompteBancaire = $numeroCompteBancaire;

        return $this;
    }
}
