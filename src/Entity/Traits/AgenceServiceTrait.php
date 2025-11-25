<?php

namespace App\Entity\Traits;

use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait pour ajouter les propriétés agence/service émetteur et débiteur aux entités
 */
trait AgenceServiceTrait
{
    /**
     * @ORM\ManyToOne(targetEntity=Agence::class)
     * @ORM\JoinColumn(name="agence_emetteur_id", referencedColumnName="id", nullable=true)
     */
    private ?Agence $agenceEmetteurId = null;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class)
     * @ORM\JoinColumn(name="service_emetteur_id", referencedColumnName="id", nullable=true)
     */
    private ?Service $serviceEmetteurId = null;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class)
     * @ORM\JoinColumn(name="agence_debiteur_id", referencedColumnName="id", nullable=true)
     */
    private ?Agence $agenceDebiteurId = null;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="domServiceDebiteur")
     * @ORM\JoinColumn(name="service_debiteur_id", referencedColumnName="id", nullable=true)
     */
    private ?Service $serviceDebiteur = null;

    /** ====================================================================
     * GETTERS & SETTERS
     *====================================================================*/

    /**
     * Récupère l'agence émettrice
     * @return Agence|null
     */
    public function getAgenceEmetteurId(): ?Agence
    {
        return $this->agenceEmetteurId;
    }

    /**
     * Définit l'agence émettrice
     * @param Agence|null $agence
     * @return self
     */
    public function setAgenceEmetteurId(?Agence $agence): self
    {
        $this->agenceEmetteurId = $agence;
        return $this;
    }

    /**
     * Récupère le service émetteur
     * @return Service|null
     */
    public function getServiceEmetteurId(): ?Service
    {
        return $this->serviceEmetteurId;
    }

    /**
     * Définit le service émetteur
     * @param Service|null $service
     * @return self
     */
    public function setServiceEmetteurId(?Service $service): self
    {
        $this->serviceEmetteurId = $service;
        return $this;
    }

    /**
     * Récupère l'agence débitrice
     * @return Agence|null
     */
    public function getAgenceDebiteurId(): ?Agence
    {
        return $this->agenceDebiteurId;
    }

    /**
     * Définit l'agence débitrice
     * @param Agence|null $agence
     * @return self
     */
    public function setAgenceDebiteurId(?Agence $agence): self
    {
        $this->agenceDebiteurId = $agence;
        return $this;
    }

    /**
     * Récupère le service débiteur
     * @return Service|null
     */
    public function getServiceDebiteur(): ?Service
    {
        return $this->serviceDebiteur;
    }

    /**
     * Définit le service débiteur
     * @param Service|null $service
     * @return self
     */
    public function setServiceDebiteur(?Service $service): self
    {
        $this->serviceDebiteur = $service;
        return $this;
    }
}
