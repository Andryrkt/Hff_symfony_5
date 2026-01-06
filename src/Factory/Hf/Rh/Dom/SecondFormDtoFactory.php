<?php

namespace App\Factory\Hf\Rh\Dom;

use DateTime;
use App\Entity\Hf\Rh\Dom\Dom;
use App\Entity\Hf\Rh\Dom\Rmq;
use App\Entity\Hf\Rh\Dom\Site;
use App\Dto\Hf\Rh\Dom\FirstFormDto;
use App\Entity\Hf\Rh\Dom\Categorie;
use App\Entity\Hf\Rh\Dom\Indemnite;
use App\Dto\Hf\Rh\Dom\SecondFormDto;
use App\Entity\Admin\PersonnelUser\User;
use App\Service\Utils\FormattingService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use App\Entity\Admin\AgenceService\Service;
use App\Entity\Admin\PersonnelUser\Personnel;
use App\Service\Utils\NumeroGeneratorService;
use Symfony\Component\Security\Core\Security;
use App\Constants\Admin\Historisation\TypeDocumentConstants;

class SecondFormDtoFactory
{
    private $security;
    private $em;
    private FormattingService $formattingService;
    private NumeroGeneratorService $numeroGeneratorService;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        FormattingService $formattingService,
        NumeroGeneratorService $numeroGeneratorService
    ) {
        $this->security = $security;
        $this->em = $em;
        $this->formattingService = $formattingService;
        $this->numeroGeneratorService = $numeroGeneratorService;
    }

    public function create(FirstFormDto $firstFormDto): SecondFormDto
    {
        $dto = new SecondFormDto();
        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        $typeMission = $firstFormDto->typeMissionId ? $this->em->find(SousTypeDocument::class, $firstFormDto->typeMissionId) : null;
        $categorie = $firstFormDto->categorieId ? $this->em->find(Categorie::class, $firstFormDto->categorieId) : null;

        $dto->dateDemande = new DateTime('now');
        $dto->typeMission = $typeMission;
        $dto->categorie = $categorie;
        $dto->matricule = $firstFormDto->matricule;
        $dto->nom = $firstFormDto->salarier == 'TEMPORAIRE' ? $firstFormDto->nom : $user->getNom();
        $dto->prenom = $firstFormDto->salarier == 'TEMPORAIRE' ? $firstFormDto->prenom : $user->getPrenoms();
        $dto->cin = $firstFormDto->cin;
        $dto->salarier = $firstFormDto->salarier;
        $dto->indemniteForfaitaire = $this->getMontantIndemniteForfaitaire($user, $typeMission, $categorie);

        $dto->rmq = $this->getRmq($user);
        $dto->site = $this->getSite($user, $typeMission, $categorie);

        /** @var Agence $agence @var Service $service */
        [$agence, $service] = $this->getAgenceService($firstFormDto, $user);
        $dto->agenceUser = $agence->getCode() . ' ' . $agence->getNom(); // ex: 01 ANTANANARIVO
        $dto->serviceUser = $service->getCode() . ' ' . $service->getNom(); // ex: INF INFORMATIQUE
        $dto->debiteur = ['agence' => $agence, 'service' => $service];

        // autres
        $dto->numeroOrdreMission = $this->numeroGeneratorService->autoGenerateNumero(TypeDocumentConstants::TYPE_DOCUMENT_DOM_CODE, true);
        $dto->mailUser = $user->getEmail();

        return $dto;
    }

    public function createFromDom(Dom $dom): SecondFormDto
    {
        $dto = new SecondFormDto();
        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        $typeMission = $dom->getSousTypeDocument();
        $categorie = $dom->getCategoryId();
        $site = $dom->getSiteId();
        $agenceEmetteur = $dom->getAgenceEmetteurId();
        $serviceEmetteur = $dom->getServiceEmetteurId();
        $agenceDebiteur = $dom->getAgenceDebiteurId();
        $serviceDebiteur = $dom->getServiceDebiteur();
        $salarier = strlen($dom->getMatricule()) === 4 && ctype_digit($dom->getMatricule()) ? 'PERMANENT' :  'TEMPORAIRE';


        $dto->dateDemande = $dom->getDateDemande();

        $dto->matricule = $dom->getMatricule();
        $dto->nom = $dom->getNom();
        $dto->prenom = $dom->getPrenom();
        $dto->salarier = $salarier;
        $dto->cin = $salarier == 'PERMANENT' ? null : trim(explode('-', $dom->getMatricule())[2]);

        $dto->typeMission = $typeMission;
        $dto->categorie = $categorie;
        $dto->site = $site;
        $dto->rmq = $this->getRmq($user);

        // dateHeureMission - créer même si certaines valeurs sont manquantes
        if ($dom->getDateDebut() || $dom->getDateFin()) {
            $dateDebut = null;
            $dateFin = null;
            $heureDebut = null;
            $heureFin = null;

            if ($dom->getDateDebut()) {
                $dateDebut = $dom->getDateDebut();
                $heureDebut = $dom->getHeureDebut() ? new \DateTime($dom->getDateDebut()->format('Y-m-d') . ' ' . $dom->getHeureDebut()) : null;
            }

            if ($dom->getDateFin()) {
                $dateFin = $dom->getDateFin();
                $heureFin = $dom->getHeureFin() ? new \DateTime($dom->getDateFin()->format('Y-m-d') . ' ' . $dom->getHeureFin()) : null;
            }

            $dto->dateHeureMission = [
                'debut' => $dateDebut,
                'fin' => $dateFin,
                'heureDebut' => $heureDebut,
                'heureFin' => $heureFin
            ];
        }

        $dto->nombreJour = $dom->getNombreJour();
        $dto->motifDeplacement = $dom->getMotifDeplacement();
        $dto->pieceJustificatif = $dom->getPieceJustificatif();
        $dto->client = $dom->getClient();
        $dto->fiche = $dom->getFiche();
        $dto->lieuIntervention = $dom->getLieuIntervention();
        $dto->vehiculeSociete = $dom->getVehiculeSociete();
        $dto->numVehicule = $dom->getNumVehicule();
        $dto->idemnityDepl = $dom->getIdemnityDepl();
        $dto->totalIndemniteDeplacement = (int)str_replace(' ', '.', $dom->getIdemnityDepl()) * (int)$dom->getNombreJour();
        $dto->devis = $dom->getDevis();
        $dto->supplementJournaliere = $dom->getDroitIndemnite();
        $dto->indemniteForfaitaire = $dom->getIndemniteForfaitaire();
        $dto->totalIndemniteForfaitaire = $dom->getTotalIndemniteForfaitaire();
        $dto->motifAutresDepense1 = $dom->getMotifAutreDepense1();
        $dto->autresDepense1 = $dom->getAutresDepense1();
        $dto->motifAutresDepense2 = $dom->getMotifAutresDepense2();
        $dto->autresDepense2 = $dom->getAutresDepense2();
        $dto->motifAutresDepense3 = $dom->getMotifAutresDepense3();
        $dto->autresDepense3 = $dom->getAutresDepense3();
        $dto->totalAutresDepenses = $dom->getTotalAutresDepenses();
        $dto->totalGeneralPayer = $dom->getTotalGeneralPayer();
        $dto->modePayement = trim(explode(':', $dom->getModePayement())[0]);
        $dto->mode = trim(explode(':', $dom->getModePayement())[1]);
        $dto->pieceJoint01 = $dom->getPieceJoint01();
        $dto->pieceJoint02 = $dom->getPieceJoint02();
        $dto->numeroOrdreMission = $dom->getNumeroOrdreMission();
        $dto->mailUser = $user->getEmail();

        $dto->agenceUser = $agenceEmetteur ? ($agenceEmetteur->getCode() . ' ' . $agenceEmetteur->getNom()) : '';
        $dto->serviceUser = $serviceEmetteur ? ($serviceEmetteur->getCode() . ' ' . $serviceEmetteur->getNom()) : '';

        // Passer les objets Entity pour le champ debiteur (le AgenceServiceType en mode hidden utilise EntityToIdTransformer)
        $dto->debiteur = ['agence' => $agenceDebiteur, 'service' => $serviceDebiteur];

        return $dto;
    }


    /**
     * recupération de l'entity Rmq par rapport au code de l'agence de l'utilisateur
     * le Rmq est 50 pour l'agence rental et STD pour les autres agences
     * 
     * @param User $user
     * @return Rmq
     */
    private function getRmq(User $user): Rmq
    {
        $agenceCode = $user->getAgenceUser()->getCode() ?? '';
        $codeToSearch = $agenceCode === (string)Agence::CODE_AGENCE_RENTAL ? (string)Agence::CODE_AGENCE_RENTAL : 'STD';

        return $this->em->getRepository(Rmq::class)->findOneBy(['description' => $codeToSearch]);
    }

    /**
     * recupération de l'entity Site par rapport au type de mission, la catégorie, le Rmq et l'indemnites
     * 
     * @param User $user
     * @param SousTypeDocument $typeMission
     * @param Categorie $categorie
     * @return Site
     */
    private function getSite(User $user, ?SousTypeDocument $typeMission, ?Categorie $categorie): ?Site
    {
        if (!$typeMission || !$categorie) {
            return null;
        }

        $criteria = [
            'sousTypeDocument' => $typeMission,
            'rmq' => $this->getRmq($user),
            'categorie' => $categorie
        ];

        $indemites = $this->em->getRepository(Indemnite::class)->findBy($criteria);

        $sites = [];
        foreach ($indemites as $value) {
            $sites[] = $value->getSite()->getNomZone();
        }

        $siteToFind = in_array(Site::NOM_ZONE_TANA, $sites) ? Site::NOM_ZONE_TANA : Site::NOM_ZONE_AUTRES_VILLES;
        $site = $this->em->getRepository(Site::class)->findOneBy(['nomZone' => $siteToFind]);

        if (!$site) {
            throw new \RuntimeException("Site '{$siteToFind}' not found in database.");
        }

        return $site;
    }

    private function getAgenceService(FirstFormDto $firstFormDto, User $user): array
    {
        if ($firstFormDto->salarier == 'TEMPORAIRE') {
            $agence = $user->getAgenceUser();
            $service = $user->getServiceUser();
        } else {
            $personnel = $this->em->getRepository(Personnel::class)->findOneBy(['matricule' => $firstFormDto->matricule]);
            if (!$personnel) {
                throw new \RuntimeException('Personnel not found for this user.');
            }

            $agenceServiceIrium = $personnel->getAgenceServiceIrium();
            if (!$agenceServiceIrium) {
                throw new \RuntimeException('AgenceServiceIrium not found for the personnel.');
            }

            $agence = $agenceServiceIrium->getAgence();
            $service = $agenceServiceIrium->getService();
        }

        return [$agence, $service];
    }

    private function getMontantIndemniteForfaitaire(User $user, ?SousTypeDocument $typeMission, ?Categorie $categorie): string
    {
        if (!$typeMission || !$categorie) {
            return '0';
        }

        $site = $this->getSite($user, $typeMission, $categorie);
        if (!$site) {
            return '0';
        }

        $criteria = [
            'sousTypeDocument' => $typeMission,
            'rmq' => $this->getRmq($user),
            'categorie' => $categorie,
            'site' => $site
        ];

        $indemites = $this->em->getRepository(Indemnite::class)->findOneBy($criteria);
        if ($indemites) {
            $montant = $indemites->getMontant();
            $montant = $this->formattingService->formatNumber($montant, 0);
        } else {
            $montant = 0;
        }

        if ($typeMission->getCodeSousType() === SousTypeDocument::CODE_TROP_PERCU) {
            $montant = 0;
        } else if ($typeMission->getCodeSousType() === SousTypeDocument::CODE_COMPLEMENT || $typeMission->getCodeSousType() === SousTypeDocument::CODE_MUTATION) {
            $montant  = '';
        }

        return (string) $montant;
    }
}
