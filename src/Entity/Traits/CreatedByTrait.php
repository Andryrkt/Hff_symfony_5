<?php

namespace App\Entity\Traits;

use App\Entity\Admin\PersonnelUser\User;
use Doctrine\ORM\Mapping as ORM;


/**
 * Trait pour ajouter la propriété createdBy aux entités
 */
trait CreatedByTrait
{
    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="id", nullable=true)
     */
    private ?User $createdBy = null;

    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @param User|null $createdBy
     * @return self
     */
    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
