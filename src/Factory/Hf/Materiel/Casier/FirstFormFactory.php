<?php


namespace App\Factory\Hf\Materiel\Casier;

use App\Entity\Admin\PersonnelUser\User;
use App\Dto\Hf\Materiel\Casier\FirstFormDto;
use Symfony\Component\Security\Core\Security;

class FirstFormFactory
{
    private Security $security;

    public function __construct(
        Security $security
    ) {
        $this->security = $security;
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


        return $dto;
    }
}
