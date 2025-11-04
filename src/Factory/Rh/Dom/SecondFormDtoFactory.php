<?php

namespace App\Factory\Rh\Dom;

use App\Dto\Rh\Dom\FirstFormDto;
use App\Dto\Rh\Dom\SecondFormDto;
use App\Entity\Admin\PersonnelUser\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class SecondFormDtoFactory
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

    public function create(FirstFormDto $firstFormDto): SecondFormDto
    {
        $dto = new SecondFormDto();
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        $dto->dateDemande = new DateTime('now');
        $dto->agenceUser = $user->getAgenceEmetteur();
        $dto->serviceUser = $user->getServiceEmetteur();
        $dto->typeMission = $firstFormDto->typeMission->getCodeSousType();
        $dto->categorie = $firstFormDto->categorie->getDescription();
        $dto->matricule = $firstFormDto->matricule;
        $dto->nom = $firstFormDto->salarier == 'TEMPORAIRE' ? $firstFormDto->nom : $user->getNom();
        $dto->prenom = $firstFormDto->salarier == 'TEMPORAIRE' ? $firstFormDto->prenom : $user->getPrenoms();
        $dto->cin = $firstFormDto->cin;

        return $dto;
    }
}
