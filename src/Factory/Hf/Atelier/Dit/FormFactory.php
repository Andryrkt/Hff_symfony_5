<?php

namespace App\Factory\Hf\Atelier\Dit;

use App\Entity\Hf\Atelier\Dit\Dit;
use App\Dto\Hf\Atelier\Dit\FormDto;
use App\Model\Hf\Atelier\Dit\DitModel;
use App\Entity\Admin\PersonnelUser\User;
use App\Service\Utils\FormattingService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Admin\AgenceService\Service;
use App\Service\Utils\NumeroGeneratorService;
use Symfony\Component\Security\Core\Security;
use App\Entity\Hf\Atelier\Dit\WorNiveauUrgence;
use App\Constants\Hf\Atelier\Dit\WorNiveauUrgenceConstants;
use App\Constants\Admin\Historisation\TypeDocumentConstants;

class FormFactory
{
    private $security;
    private $em;
    private NumeroGeneratorService $numeroGeneratorService;
    private FormattingService $formattingService;
    private DitModel $ditModel;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        NumeroGeneratorService $numeroGeneratorService,
        FormattingService $formattingService,
        DitModel $ditModel
    ) {
        $this->security = $security;
        $this->em = $em;
        $this->numeroGeneratorService = $numeroGeneratorService;
        $this->formattingService = $formattingService;
        $this->ditModel = $ditModel;
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

        $dto->emetteur = [
            'agence' => $agence,
            'service' => $service
        ];
        $dto->debiteur = [
            'agence' => $agence,
            'service' => $service
        ];
        $dto->niveauUrgence = $this->em->getRepository(WorNiveauUrgence::class)
            ->findOneBy(['code' => WorNiveauUrgenceConstants::NIVEAU_URGENCE_P2]);
        $dto->demandeDevis = 'NON';
        $dto->clientSousContrat = 'NON';

        $dto->dateDemande = new \DateTime();
        $dto->numeroDit = $this->numeroGeneratorService->autoGenerateNumero(TypeDocumentConstants::TYPE_DOCUMENT_DIT_CODE, true);
        $dto->mailDemandeur = $user->getEmail();
        $dto->statutDemande = $this->em->getRepository(StatutDemande::class)->findOneBy(['codeApplication' => TypeDocumentConstants::TYPE_DOCUMENT_DIT_CODE, 'description' => 'A AFFECTER']);

        $this->enrichDtoWithMaterielInfo($dto);

        return $dto;
    }

    public function duplicate(string $numDit): FormDto
    {
        $dto = new FormDto();
        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        $dit = $this->em->getRepository(Dit::class)->findOneBy(['numeroDit' => $numDit]);


        $dto->emetteur = [
            'agence' => $dit->getAgenceEmetteurId(),
            'service' => $dit->getServiceEmetteurId()
        ];
        $dto->debiteur = [
            'agence' => $dit->getAgenceDebiteurId(),
            'service' => $dit->getServiceDebiteur()
        ];
        $dto->niveauUrgence = $dit->getWorNiveauUrgence();
        $dto->demandeDevis = $dit->getDemandeDevis();
        $dto->clientSousContrat = $dit->getClientSousContrat();

        $dto->dateDemande = new \DateTime();
        $dto->numeroDit = $this->numeroGeneratorService->autoGenerateNumero(TypeDocumentConstants::TYPE_DOCUMENT_DIT_CODE, true);
        $dto->mailDemandeur = $user->getEmail();
        $dto->statutDemande = $this->em->getRepository(StatutDemande::class)
            ->findOneBy(['codeApplication' => TypeDocumentConstants::TYPE_DOCUMENT_DIT_CODE, 'description' => 'A AFFECTER']);

        // recupération des informations pour le duplication
        $dto->objetDemande = $dit->getObjectDemande();
        $dto->detailDemande = $dit->getDetailDemande();
        $dto->typeDocument = $dit->getWorTypeDocument();
        $dto->categorieDemande = $dit->getCategorieAteApp();
        $dto->interneExterne = $dit->getInterneExterne();
        $dto->demandeDevis = $dit->getDemandeDevis();
        $dto->livraisonPartiel = $dit->getLivraisonPartiel();
        $dto->avisRecouvrement = $dit->getAvisRecouvrement();
        $dto->datePrevueTravaux = $dit->getDatePrevueTravaux();
        $dto->typeReparation = $dit->getTypeReparation();
        $dto->reparationRealise = $dit->getReparationRealise();


        $dto->numeroClient = $dit->getNumeroClient();
        $dto->nomClient = $dit->getNomClient();
        $dto->numeroTel = $dit->getNumeroTelClient();
        $dto->mailClient = $dit->getMailClient();
        $dto->clientSousContrat = $dit->getClientSousContrat();

        $dto->pieceJoint01 = $dit->getPieceJoint01();
        $dto->pieceJoint02 = $dit->getPieceJoint02();
        $dto->pieceJoint03 = $dit->getPieceJoint03();

        $dto->idMateriel = $dit->getIdMateriel();

        $this->enrichDtoWithMaterielInfo($dto);

        return $dto;
    }

    public function enrichDtoWithMaterielInfo(FormDto $dto): void
    {
        if ($dto->idMateriel === null) {
            return;
        }

        $dto->historiqueMateriel = $this->historiqueInterventionMateriel($dto->idMateriel, $dto->reparationRealise);

        $infoMateriel = $this->ditModel->getInfoMateriel($dto);
        if ($infoMateriel) {
            $dto->coutAcquisition = $infoMateriel['cout_acquisition'];
            $dto->amortissement = $infoMateriel['amortissement'];
            $dto->valeurNetComptable = $dto->getValeurNetComptable();
            $dto->chargeEntretient = $infoMateriel['charge_entretien'];
            $dto->chargeLocative = $infoMateriel['charge_locative'];
            $dto->chiffreAffaire = $infoMateriel['chiffre_affaires'];
            $dto->resultatExploitation = $dto->getResultatExploitation();

            $dto->modele = $infoMateriel['modele'];
            $dto->designation = $infoMateriel['designation'];
            $dto->constructeur = $infoMateriel['constructeur'];
            $dto->casier = $infoMateriel['casier_emetteur'];
            $dto->marque = $infoMateriel['marque'];
            $dto->idMateriel = $infoMateriel['num_matricule'];
            $dto->numSerie = $infoMateriel['num_serie'];
            $dto->numParc = $infoMateriel['num_parc'];
            $dto->heureMachine = $infoMateriel['heure'];
            $dto->kmMachine = $infoMateriel['km'];
        }
    }

    private function historiqueInterventionMateriel(?int $idMateriel, ?string $reparationRealise): array
    {
        if ($idMateriel === null || $reparationRealise === null) {
            return [];
        }

        $historiqueMateriel = $this->ditModel->getHistoriqueMateriel($idMateriel, $reparationRealise);

        foreach ($historiqueMateriel as $keys => $values) {
            foreach ($values as $key => $value) {
                if ($key == "datedebut") {
                    $historiqueMateriel[$keys]['datedebut'] = implode('/', array_reverse(explode("-", $value)));
                } elseif ($key === 'somme') {
                    $historiqueMateriel[$keys][$key] = explode(',', $this->formattingService->formatNumber($value))[0];
                }
            }
        }
        return $historiqueMateriel;
    }
}
