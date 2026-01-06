<?php

namespace App\Factory\Hf\Materiel\Badm;

use App\Entity\Admin\PersonnelUser\User;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use App\Dto\Hf\Materiel\Badm\FirstFormDto;
use Symfony\Component\Security\Core\Security;

class SecondFormFactory
{
    private Security $security;

    public function __construct(
        Security $security
    ) {
        $this->security = $security;
    }

    public function create(FirstFormDto $firstFormDto, array $infoMaterielDansIps): SecondFormDto
    {
        $dto =  new SecondFormDto();

        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }


        // carecteristique du materiel
        $dto->designation = $infoMaterielDansIps['designation'];
        $dto->idMateriel = $infoMaterielDansIps['num_matricule'];
        $dto->numParc = $infoMaterielDansIps['num_parc'];
        $dto->numSerie = $infoMaterielDansIps['num_serie'];
        $dto->groupe = $infoMaterielDansIps['famille'];
        $dto->constructeur = $infoMaterielDansIps['constructeur'];
        $dto->modele = $infoMaterielDansIps['modele'];
        $dto->anneeDuModele = $infoMaterielDansIps['annee_du_modele'];
        $dto->affectation = $infoMaterielDansIps['affectation'];
        $dto->dateAchat = $infoMaterielDansIps['date_achat'];

        // etat machine
        $dto->heureMachine = $infoMaterielDansIps['heure_machine'];
        $dto->kmMachine = $infoMaterielDansIps['km_machine'];

        // entrer en parc
        $dto->etatAchat = $infoMaterielDansIps['etat_achat'];
        $dto->dateMiseLocation = $infoMaterielDansIps['date_mise_location'];

        //valeur
        $dto->coutAcquisition = $infoMaterielDansIps['cout_acquisition'];
        $dto->amortissement = $infoMaterielDansIps['amortissement'];
        $dto->valeurNetComptable = $infoMaterielDansIps['valeur_net_comptable'];

        // Mouvement materiel
        $dto->dateDemande = new \DateTime();
        $dto->typeMouvement = $firstFormDto->typeMouvement;

        return $dto;
    }
}
