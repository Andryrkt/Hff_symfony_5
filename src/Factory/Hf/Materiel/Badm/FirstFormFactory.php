<?php

namespace App\Factory\Hf\Materiel\Badm;

use App\Entity\Admin\PersonnelUser\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\Hf\Materiel\Badm\FirstFormDto;
use Symfony\Component\Security\Core\Security;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;
use App\Constants\Hf\Materiel\Badm\TypeMouvementConstants;

class FirstFormFactory
{
    private Security $security;
    private EntityManagerInterface $em;

    public function __construct(
        Security $security,
        EntityManagerInterface $em
    ) {
        $this->security = $security;
        $this->em = $em;
    }

    public function create(): FirstFormDto
    {
        $dto =  new FirstFormDto();

        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        $dto->agenceUser = $user->getAgenceUser()->getCode() . '-' . $user->getAgenceUser()->getNom();
        $dto->serviceUser = $user->getServiceUser()->getCode() . '-' . $user->getServiceUser()->getNom();
        $dto->typeMouvement = $this->em->getRepository(TypeMouvement::class)->findOneBy(['description' => TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC]);


        return $dto;
    }
}
