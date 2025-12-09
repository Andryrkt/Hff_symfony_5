<?php

namespace App\Contract\Entity;

use App\Entity\Admin\PersonnelUser\User;



/**
 * Interface pour les entités qui doivent tracer leur créateur
 */
interface CreatedByInterface
{
    /**
     * Définit l'utilisateur qui a créé l'entité
     *
     * @param User|null $createdBy
     * @return self
     */
    public function setCreatedBy(?User $createdBy): self;

    /**
     * Récupère l'utilisateur qui a créé l'entité
     *
     * @return User|null
     */
    public function getCreatedBy(): ?User;
}
