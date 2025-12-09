<?php

namespace App\EventListener;

use App\Contract\Entity\CreatedByInterface;
use App\Entity\Admin\PersonnelUser\User;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Listener Doctrine qui définit automatiquement le createdBy lors de la création d'une entité
 */
class CreatedByListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Appelé avant la persistance d'une nouvelle entité
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // Vérifie si l'entité implémente CreatedByInterface
        if (!$entity instanceof CreatedByInterface) {
            return;
        }

        // Si createdBy n'est pas déjà défini, on le définit automatiquement
        if ($entity->getCreatedBy() === null) {
            /** @var User $user */
            $user = $this->security->getUser();
            if ($user !== null) {
                $entity->setCreatedBy($user);
            }
        }
    }
}
