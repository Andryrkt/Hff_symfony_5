<?php

namespace App\Contract\Entity;

use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;

/**
 * Interface pour les entités qui ont des agences et services émetteurs/débiteurs
 */
interface AgenceServiceInterface
{
    /**
     * Récupère l'agence émettrice
     * @return Agence|null
     */
    public function getAgenceEmetteurId(): ?Agence;

    /**
     * Définit l'agence émettrice
     * @param Agence|null $agence
     * @return self
     */
    public function setAgenceEmetteurId(?Agence $agence): self;

    /**
     * Récupère le service émetteur
     * @return Service|null
     */
    public function getServiceEmetteurId(): ?Service;

    /**
     * Définit le service émetteur
     * @param Service|null $service
     * @return self
     */
    public function setServiceEmetteurId(?Service $service): self;

    /**
     * Récupère l'agence débitrice
     * @return Agence|null
     */
    public function getAgenceDebiteurId(): ?Agence;

    /**
     * Définit l'agence débitrice
     * @param Agence|null $agence
     * @return self
     */
    public function setAgenceDebiteurId(?Agence $agence): self;

    /**
     * Récupère le service débiteur
     * @return Service|null
     */
    public function getServiceDebiteur(): ?Service;

    /**
     * Définit le service débiteur
     * @param Service|null $service
     * @return self
     */
    public function setServiceDebiteur(?Service $service): self;
}
