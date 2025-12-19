<?php


namespace App\Factory\Hf\Materiel\Casier;

use App\Entity\Admin\PersonnelUser\User;
use App\Dto\Hf\Materiel\Casier\SecondFormDto;
use Symfony\Component\Security\Core\Security;

class SecondFormFactory
{
    private Security $security;

    public function __construct(
        Security $security
    ) {
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

        $dto->dateDemande = new \DateTime();
        $dto->designation = $caracteristiqueMateriel['designation'];
        $dto->idMateriel = $caracteristiqueMateriel['num_matricule'];
        $dto->numParc = $caracteristiqueMateriel['num_parc'];
        $dto->numSerie = $caracteristiqueMateriel['num_serie'];
        $dto->groupe = $caracteristiqueMateriel['famille'];
        $dto->constructeur = $caracteristiqueMateriel['constructeur'];
        $dto->modele = $caracteristiqueMateriel['modele'];
        $dto->anneeDuModele = $caracteristiqueMateriel['annee_du_modele'];
        $dto->affectation = $caracteristiqueMateriel['affectation'];
        $dto->dateAchat = $caracteristiqueMateriel['date_achat'];

        return $dto;
    }
}
