<?php

namespace App\Factory\Hf\Rh\Dom;

use App\Dto\Hf\Rh\Dom\FirstFormDto;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class FirstFormDtoFactory
{
    private $security;
    private $em;
    public function __construct(
        Security $security,
        EntityManagerInterface $em
    ) {
        $this->security = $security;
        $this->em = $em;
    }

    public function create(): FirstFormDto
    {
        $dto = new FirstFormDto();
        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        $dto->agenceUser = $user->getAgenceUser()->getCode() . '-' . $user->getAgenceUser()->getNom();
        $dto->serviceUser = $user->getServiceUser()->getCode() . '-' . $user->getServiceUser()->getNom();
        $dto->typeMission = $this->em->getRepository(SousTypeDocument::class)
            ->findOneBy(['codeSousType' => 'MISSION']);

        return $dto;
    }
}
