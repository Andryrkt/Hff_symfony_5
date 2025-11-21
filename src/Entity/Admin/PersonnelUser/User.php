<?php

namespace App\Entity\Admin\PersonnelUser;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use Doctrine\Common\Collections\Collection;
use App\Entity\Admin\ApplicationGroupe\Group;
use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Admin\PersonnelUser\UserAccess;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Admin\ApplicationGroupe\Permission;
use App\Repository\Admin\PersonnelUser\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $matricule;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numero_telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $poste;

    /**
          * @ORM\OneToOne(targetEntity=Personnel::class, inversedBy="users", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="personnel_id", referencedColumnName="id")
     */
    private $personnel;

    /**
     * @ORM\OneToMany(targetEntity=UserAccess::class, mappedBy="users")
     */
    private $userAccesses;

    /**
     * @ORM\ManyToMany(targetEntity=Permission::class, inversedBy="users", fetch="EAGER")
     */
    private $permissionsDirectes;


    public function __construct()
    {
        $this->userAccesses = new ArrayCollection();
        $this->permissionsDirectes = new ArrayCollection();
    }

    /**=================================================
     * Fonction personaliser
     *================================================*/

    /**
     * Récupération de l'agence de l'utilisateur conecter
     *
     * @return string|null
     */
    public function getAgenceUser(): ?Agence
    {
        $personnel = $this->getPersonnel();
        if (!$personnel) {
            throw new \RuntimeException('Personnel not found for this user.');
        }

        $agenceServiceIrium = $personnel->getAgenceServiceIrium();
        if (!$agenceServiceIrium) {
            throw new \RuntimeException('AgenceServiceIrium not found for the personnel.');
        }

        $agence = $agenceServiceIrium->getAgence();
        if (!$agence) {
            throw new \RuntimeException('Agence not found for the AgenceServiceIrium.');
        }

        return $agence;
    }

    /**
     * Recuperation du service de l'utilisateur connecter
     *
     * @return string|null
     */
    public function getServiceUser(): ?Service
    {
        $personnel = $this->getPersonnel();
        if (!$personnel) {
            throw new \RuntimeException('Personnel not found for this user.');
        }

        $agenceServiceIrium = $personnel->getAgenceServiceIrium();
        if (!$agenceServiceIrium) {
            throw new \RuntimeException('AgenceServiceIrium not found for the personnel.');
        }

        $service = $agenceServiceIrium->getService();
        if (!$service) {
            throw new \RuntimeException('Service not found for the AgenceServiceIrium.');
        }

        return $service;
    }

    /**
     * Récupération du nom de l'utilisateur connecter
     *
     * @return string
     */
    public function getNom(): string
    {
        $personnel = $this->getPersonnel();
        if (!$personnel) {
            throw new \RuntimeException('Personnel not found for this user.');
        }

        return $personnel->getNom();
    }

    /**
     * Récupération du prénoms de l'utilisateur connecter
     *
     * @return string
     */
    public function getPrenoms(): string
    {
        $personnel = $this->getPersonnel();
        if (!$personnel) {
            throw new \RuntimeException('Personnel not found for this user.');
        }

        return $personnel->getPrenoms();
    }
    /** ==================================================
     *  GETTERS & SETTERS
     *==================================================*/

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(?string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getNumeroTelephone(): ?string
    {
        return $this->numero_telephone;
    }

    public function setNumeroTelephone(?string $numero_telephone): self
    {
        $this->numero_telephone = $numero_telephone;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(?string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;

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
            $userAccess->setUsers($this);
        }

        return $this;
    }

    public function removeUserAccess(UserAccess $userAccess): self
    {
        if ($this->userAccesses->removeElement($userAccess)) {
            // set the owning side to null (unless already changed)
            if ($userAccess->getUsers() === $this) {
                $userAccess->setUsers(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Permission>
     */
    public function getPermissionsDirectes(): Collection
    {
        return $this->permissionsDirectes;
    }

    public function addPermissionsDirecte(Permission $permissionsDirecte): self
    {
        if (!$this->permissionsDirectes->contains($permissionsDirecte)) {
            $this->permissionsDirectes[] = $permissionsDirecte;
        }

        return $this;
    }

    public function removePermissionsDirecte(Permission $permissionsDirecte): self
    {
        $this->permissionsDirectes->removeElement($permissionsDirecte);

        return $this;
    }
}
