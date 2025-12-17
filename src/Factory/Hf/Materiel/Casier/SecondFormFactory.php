<?php


namespace App\Factory\Hf\Materiel\Casier;

use App\Entity\Admin\PersonnelUser\User;
use App\Dto\Hf\Materiel\Casier\SecondFormDto;
use App\Service\Utils\NumeroGeneratorService;
use Symfony\Component\Security\Core\Security;
use App\Repository\Admin\Statut\StatutDemandeRepository;

class SecondFormFactory
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

    public function create(array $caracteristiqueMateriel): SecondFormDto
    {
        $dto =  new SecondFormDto();

        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        $dto->agenceUser = $user->getAgenceUser()->getCode() . '-' . $user->getAgenceUser()->getNom();
        $dto->serviceUser = $user->getServiceUser()->getCode() . '-' . $user->getServiceUser()->getNom();
        $dto->designation = $caracteristiqueMateriel['designation'];
        $dto->idMateriel = $caracteristiqueMateriel['num_matricule'];
        $dto->numParc = $caracteristiqueMateriel['num_parc'];
        $dto->numSerie = $caracteristiqueMateriel['num_serie'];
        $dto->groupe = $caracteristiqueMateriel['groupe'];
        $dto->constructeur = $caracteristiqueMateriel['constructeur'];
        $dto->modele = $caracteristiqueMateriel['modele'];
        $dto->anneeDuModele = $caracteristiqueMateriel['annee_du_modele'];
        $dto->affectation = $caracteristiqueMateriel['affectation'];
        $dto->dateAchat = $caracteristiqueMateriel['date_achat'];

        return $dto;
    }
}
