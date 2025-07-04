<?php

namespace App\Entity\Admin\ApplicationGroupe;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Admin\ApplicationGroupe\Group;
use App\Entity\Admin\ApplicationGroupe\Application;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;

/**
 * @ORM\Entity()
 */
class GroupAccess
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="groupAccesses")
     */
    private $group;

    /**
     * @ORM\ManyToOne(targetEntity=Application::class)
     */
    private $application;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class)
     */
    private $agence;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class)
     */
    private $service;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $accessType; // ALL, AGENCE, SERVICE, AGENCE_SERVICE

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;
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
