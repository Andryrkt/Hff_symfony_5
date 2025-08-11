<?php

namespace App\Entity\Admin\AgenceService;

use App\Entity\Dom\DemandeOrdreMission;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Admin\AgenceService\AgenceRepository;
use App\Entity\Traits\TimestampableTrait;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use App\Entity\Admin\AgenceService\Service;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\AgenceServicesController;

/**
 * @Broadcast()
 * @ORM\Entity(repositoryClass=AgenceRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={
 *         "get",
 *         "get_services"={
 *             "method"="GET",
 *             "path"="/agences/{id}/services",
 *             "controller"=AgenceServicesController::class,
 *             "security"="is_granted('ROLE_USER')",
 *             "normalization_context"={"groups"={"service:read"}},
 *             "openapi_context"={
 *                 "summary"="Retrieves the collection of Service resources for a given Agence.",
 *                 "parameters"={
 *                     {
 *                         "name"="id",
 *                         "in"="path",
 *                         "required"=true,
 *                         "schema"={
 *                             "type"="integer"
 *                         }
 *                     }
 *                 }
 *             }
 *         }
 *     }
 * )
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
     * @ORM\OneToMany(targetEntity=AgenceServiceIrium::class, mappedBy="agence")
     */
    private $agenceServiceIriums;

    /**
     * @ORM\OneToMany(targetEntity=UserAccess::class, mappedBy="agence")
     */
    private $userAccesses;

    /**
     * @ORM\ManyToMany(targetEntity=Service::class, inversedBy="agences")
     * @ORM\JoinTable(name="agence_service")
     */
    private $services;

    /**
     * @ORM\OneToMany(targetEntity=DemandeOrdreMission::class, mappedBy="agenceEmetteurId")
     */
    private $domEmetteur;

    /**
     * @ORM\OneToMany(targetEntity=DemandeOrdreMission::class, mappedBy="agenceDebiteurId")
     */
    private $domDebiteur;

    public function __construct()
    {
        $this->agenceServiceIriums = new ArrayCollection();
        $this->userAccesses = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->domEmetteur = new ArrayCollection();
        $this->domDebiteur = new ArrayCollection();
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
            $agenceServiceIrium->setAgence($this);
        }

        return $this;
    }

    public function removeAgenceServiceIrium(AgenceServiceIrium $agenceServiceIrium): self
    {
        if ($this->agenceServiceIriums->removeElement($agenceServiceIrium)) {
            // set the owning side to null (unless already changed)
            if ($agenceServiceIrium->getAgence() === $this) {
                $agenceServiceIrium->setAgence(null);
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

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->addAgence($this);
        }
        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            $service->removeAgence($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, DemandeOrdreMission>
     */
    public function getDomEmetteur(): Collection
    {
        return $this->domEmetteur;
    }

    public function addDomEmetteur(DemandeOrdreMission $domEmetteur): self
    {
        if (!$this->domEmetteur->contains($domEmetteur)) {
            $this->domEmetteur[] = $domEmetteur;
            $domEmetteur->setAgenceEmetteurId($this);
        }

        return $this;
    }

    public function removeDomEmetteur(DemandeOrdreMission $domEmetteur): self
    {
        if ($this->domEmetteur->removeElement($domEmetteur)) {
            // set the owning side to null (unless already changed)
            if ($domEmetteur->getAgenceEmetteurId() === $this) {
                $domEmetteur->setAgenceEmetteurId(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, DemandeOrdreMission>
     */
    public function getDomDebiteur(): Collection
    {
        return $this->domDebiteur;
    }

    public function addDomDebiteur(DemandeOrdreMission $domDebiteur): self
    {
        if (!$this->domDebiteur->contains($domDebiteur)) {
            $this->domDebiteur[] = $domDebiteur;
            $domDebiteur->setAgenceEmetteurId($this);
        }

        return $this;
    }

    public function removeDomDebiteur(DemandeOrdreMission $domDebiteur): self
    {
        if ($this->domDebiteur->removeElement($domDebiteur)) {
            // set the owning side to null (unless already changed)
            if ($domDebiteur->getAgenceEmetteurId() === $this) {
                $domDebiteur->setAgenceEmetteurId(null);
            }
        }

        return $this;
    }
}
