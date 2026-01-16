<?php

namespace App\Factory\Hf\Atelier\Dit;

use App\Dto\Hf\Atelier\Dit\FormDto;
use App\Entity\Admin\PersonnelUser\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use Symfony\Component\Security\Core\Security;
use App\Entity\Hf\Atelier\Dit\WorNiveauUrgence;
use App\Constants\Hf\Dit\WorNiveauUrgenceConstants;

class FormFactory
{
    private $security;
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    public function create(): FormDto
    {
        $dto = new FormDto();
        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        /** @var Agence $agence @var Service $service */
        $agence = $user->getAgenceUser();
        $service = $user->getServiceUser();
        $dto->agenceUser = $agence->getCode() . ' ' . $agence->getNom(); // ex: 01 ANTANANARIVO
        $dto->serviceUser = $service->getCode() . ' ' . $service->getNom(); // ex: INF INFORMATIQUE
        $dto->debiteur = [
            'agence' => $agence,
            'service' => $service
        ];
        $dto->worNiveauUrgence = $this->em->getRepository(WorNiveauUrgence::class)
            ->findOneBy(['code' => WorNiveauUrgenceConstants::NIVEAU_URGENCE_P1]);

        return $dto;
    }
}
