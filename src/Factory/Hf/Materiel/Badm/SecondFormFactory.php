<?php

namespace App\Factory\Hf\Materiel\Badm;

use App\Entity\Admin\PersonnelUser\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hf\Materiel\Casier\Casier;
use App\Dto\Hf\Materiel\Badm\FirstFormDto;
use App\Entity\Admin\AgenceService\Agence;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use App\Entity\Admin\AgenceService\Service;
use App\Service\Utils\NumeroGeneratorService;
use Symfony\Component\Security\Core\Security;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;
use App\Constants\Admin\AgenceService\AgenceConstants;
use App\Constants\Admin\AgenceService\ServiceConstants;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use App\Constants\Hf\Materiel\Badm\TypeMouvementConstants;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Model\Hf\Materiel\Badm\BadmModel;

class SecondFormFactory
{
    private NumeroGeneratorService $numeroGeneratorService;
    private StatutDemandeRepository $statutDemandeRepository;
    private Security $security;
    private EntityManagerInterface $entityManager;
    private BadmModel $badmModel;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        NumeroGeneratorService $numeroGeneratorService,
        StatutDemandeRepository $statutDemandeRepository,
        BadmModel $badmModel
    ) {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->numeroGeneratorService = $numeroGeneratorService;
        $this->statutDemandeRepository = $statutDemandeRepository;
        $this->badmModel = $badmModel;
    }

    public function create(FirstFormDto $firstFormDto, array $infoMaterielDansIps): SecondFormDto
    {
        $dto =  new SecondFormDto();

        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        $agenceServiceCasierDateMiseEnLocation = $this->AgenceServiceCasierDateMiseEnLocation($firstFormDto->typeMouvement, $infoMaterielDansIps);

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

        //Agence - service - casier emetteur
        $dto->emetteur = [
            'agence' => $agenceServiceCasierDateMiseEnLocation['agenceEmetteur'],
            'service' => $agenceServiceCasierDateMiseEnLocation['serviceEmetteur'],
            'casier' => $agenceServiceCasierDateMiseEnLocation['casierEmetteur']
        ];

        //Agence -service - casier debiteur
        $dto->destinataire = [
            'agence' => $agenceServiceCasierDateMiseEnLocation['agenceDestinataire'],
            'service' => $agenceServiceCasierDateMiseEnLocation['serviceDestinataire'],
            'casier' => $agenceServiceCasierDateMiseEnLocation['casierDestinataire']
        ];

        // entrer en parc
        $dto->etatAchat = $infoMaterielDansIps['etat_achat'] === 'O' ? 'OCCASION' : 'NEUF';
        $dto->dateMiseLocation = $agenceServiceCasierDateMiseEnLocation['dateMiseLocation'];

        //valeur
        $coutAcquisition = (float)$infoMaterielDansIps['cout_acquisition'];
        $amortissement = (float)$infoMaterielDansIps['amortissement'];
        $dto->coutAcquisition = $coutAcquisition;
        $dto->amortissement = $amortissement;
        $dto->valeurNetComptable = $coutAcquisition - $amortissement;

        // Mouvement materiel
        $dto->dateDemande = new \DateTime();
        $dto->typeMouvement = $firstFormDto->typeMouvement;
        $dto->numeroBadm = $this->numeroGeneratorService->autoGenerateNumero(TypeDocumentConstants::TYPE_DOCUMENT_BADM_CODE, true);
        $dto->statutDemande = $this->statutDemandeRepository->findOneBy(['codeApplication' => TypeDocumentConstants::TYPE_DOCUMENT_BADM_CODE, 'description' => 'OUVERT']);
        $dto->mailUser = $user->getEmail();

        //OR
        $ors = $this->badmModel->getInfoOr($infoMaterielDansIps['num_matricule']);
        $dto->estOr = empty($ors) ? 'NON' : 'OUI';
        $dto->ors = $ors;

        return $dto;
    }

    private function AgenceServiceCasierDateMiseEnLocation(TypeMouvement $typeMouvement, array $infoMaterielDansIps): array
    {
        $agenceEmetteur = $this->entityManager->getRepository(Agence::class)->findOneBy(['code' => $infoMaterielDansIps['code_agence']]);
        $serviceEmetteur = $this->entityManager->getRepository(Service::class)->findOneBy(['code' => $infoMaterielDansIps['code_service']]);
        $casierEmetteur = $this->entityManager->getRepository(Casier::class)->findOneBy(['nom' => $infoMaterielDansIps['casier_emetteur'] ?? null]);

        $agenceDestinataire = null;
        $serviceDestinataire = null;
        $casierDestinataire = null;
        $dateMiseLocation = null;

        //Agence destinataire - service destinataire - casier destinataire - dateMiseLocation - service emetteur
        if ($typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC) {
            $serviceEmetteur = $this->entityManager->getRepository(Service::class)->findOneBy(['code' => ServiceConstants::CODE_SERVICE_COMMERCIAL]);
        } elseif ($typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE) {
            $dateMiseLocation = $this->dateMiseEnlocation($infoMaterielDansIps);
        } elseif ($typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_DE_CASIER) {
            $agenceDestinataire = $agenceEmetteur;
            $serviceDestinataire = $serviceEmetteur;
            $dateMiseLocation = $this->dateMiseEnlocation($infoMaterielDansIps);
        } elseif ($typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF) {
            if (in_array($agenceEmetteur->getCode(), AgenceConstants::CODE_AGENCE_ENERGIE)) {
                $agenceDestinataire = $this->entityManager->getRepository(Agence::class)->findOneBy(['code' => AgenceConstants::CODE_AGENCE_COMM_ENERGIE]);
                $serviceDestinataire = $this->entityManager->getRepository(Service::class)->findOneBy(['code' => ServiceConstants::CODE_SERVICE_COMMERCIAL]);
            } else {
                $agenceDestinataire = $this->entityManager->getRepository(Agence::class)->findOneBy(['code' => AgenceConstants::CODE_AGENCE_ANTANARIVO]);
                $serviceDestinataire = $this->entityManager->getRepository(Service::class)->findOneBy(['code' => ServiceConstants::CODE_SERVICE_COMMERCIAL]);
            }
            $dateMiseLocation = $this->dateMiseEnlocation($infoMaterielDansIps);
        } elseif ($typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT) {
            $agenceDestinataire = $agenceEmetteur;
            $serviceDestinataire = $serviceEmetteur;
            $casierDestinataire = $casierEmetteur;
            $dateMiseLocation = $this->dateMiseEnlocation($infoMaterielDansIps);
        }

        return [
            'agenceDestinataire' => $agenceDestinataire,
            'serviceDestinataire' => $serviceDestinataire,
            'casierDestinataire' => $casierDestinataire,
            'dateMiseLocation' => $dateMiseLocation,
            'agenceEmetteur' => $agenceEmetteur,
            'serviceEmetteur' => $serviceEmetteur,
            'casierEmetteur' => $casierEmetteur
        ];
    }

    private function dateMiseEnlocation(array $infoMaterielDansIps): ?\DateTime
    {
        return $infoMaterielDansIps['date_mise_en_location'] === null ? null : \DateTime::createFromFormat('Y-m-d', $infoMaterielDansIps['date_mise_en_location']);
    }
}
