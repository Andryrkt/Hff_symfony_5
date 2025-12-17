<?php


namespace App\Factory\Hf\Materiel\Casier;

use App\Entity\Admin\PersonnelUser\User;
use App\Dto\Hf\Materiel\Casier\SecondFormDto;
use App\Service\Utils\NumeroGeneratorService;
use Symfony\Component\Security\Core\Security;
use App\Repository\Admin\Statut\StatutDemandeRepository;

class CasierSecondFormFactory
{
    private NumeroGeneratorService $numeroGeneratorService;
    private StatutDemandeRepository $statutDemandeRepository;
    private Security $security;

    public function __construct(
        NumeroGeneratorService $numeroGeneratorService,
        StatutDemandeRepository $statutDemandeRepository,
        Security $security
    ) {
        $this->numeroGeneratorService = $numeroGeneratorService;
        $this->statutDemandeRepository = $statutDemandeRepository;
        $this->security = $security;
    }

    public function create(): SecondFormDto
    {
        $dto =  new SecondFormDto();

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
