<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;

trait AgenceServiceTrait
{
    /**
     * @ORM\ManyToOne(targetEntity=Agence::class)
     * @ORM\JoinColumn(name="agence_emetteur_id", referencedColumnName="id")
     */
    private  $agenceEmetteurId;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class)
     * @ORM\JoinColumn(name="service_emetteur_id", referencedColumnName="id")
     */
    private  $serviceEmetteurId;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class)
     * @ORM\JoinColumn(name="agence_debiteur_id", referencedColumnName="id")
     */
    private  $agenceDebiteurId;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="domServiceDebiteur")
     * @ORM\JoinColumn(name="service_debiteur_id", referencedColumnName="id")
     */
    private  $serviceDebiteurId;

    /** ====================================================================
     * GETTERS & SETTERS
     *====================================================================*/

    /**
     * Get the value of agenceEmetteurId
     */
    public function getAgenceEmetteurId()
    {
        return $this->agenceEmetteurId;
    }

    /**
     * Set the value of agenceEmetteurId
     *
     * @return  self
     */
    public function setAgenceEmetteurId($agenceEmetteurId)
    {
        $this->agenceEmetteurId = $agenceEmetteurId;

        return $this;
    }

    /**
     * Get the value of serviceEmetteurId
     */
    public function getServiceEmetteurId()
    {
        return $this->serviceEmetteurId;
    }

    /**
     * Set the value of serviceEmetteurId
     *
     * @return  self
     */
    public function setServiceEmetteurId($serviceEmetteurId)
    {
        $this->serviceEmetteurId = $serviceEmetteurId;

        return $this;
    }

    /**
     * Get the value of agenceDebiteurId
     */
    public function getAgenceDebiteurId()
    {
        return $this->agenceDebiteurId;
    }

    /**
     * Set the value of agenceDebiteurId
     *
     * @return  self
     */
    public function setAgenceDebiteurId($agenceDebiteurId)
    {
        $this->agenceDebiteurId = $agenceDebiteurId;

        return $this;
    }

    /**
     * Get the value of serviceDebiteurId
     */
    public function getServiceDebiteurId()
    {
        return $this->serviceDebiteurId;
    }

    /**
     * Set the value of serviceDebiteurId
     *
     * @return  self
     */
    public function setServiceDebiteurId($serviceDebiteurId)
    {
        $this->serviceDebiteurId = $serviceDebiteurId;

        return $this;
    }
}
