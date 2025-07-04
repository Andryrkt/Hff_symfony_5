<?php

namespace App\Entity\Admin\PersonnelUser;

use App\Repository\Admin\PersonnelUser\UserAccessRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use App\Entity\Admin\ApplicationGroupe\Application;

/**
 * @ORM\Entity(repositoryClass=UserAccessRepository::class)
 */
class UserAccess
{
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
     * @ORM\ManyToOne(targetEntity=Application::class, inversedBy="userAccesses")
     */
    private $application;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $accessType; // ALL, AGENCE, SERVICE, AGENCE_SERVICE

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

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getAccessType(): ?string
    {
        return $this->accessType;
    }

    public function setAccessType(string $accessType): self
    {
        $this->accessType = $accessType;

        return $this;
    }
}
