<?php

namespace App\Factory\Hf\Rh\Dom;

use DateTime;
use App\Entity\Hf\Rh\Dom\Dom;
use App\Entity\Hf\Rh\Dom\Rmq;
use App\Entity\Hf\Rh\Dom\Site;
use App\Dto\Hf\Rh\Dom\FirstFormDto;
use App\Entity\Hf\Rh\Dom\Indemnite;
use App\Dto\Hf\Rh\Dom\SecondFormDto;
use App\Entity\Admin\PersonnelUser\User;
use App\Service\Utils\FormattingService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
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

        if ($firstFormDto->typeMission) {
            // Réattacher l'entité au contexte Doctrine si elle est détachée (vient de la session)
            $firstFormDto->typeMission = $this->em->find(
                SousTypeDocument::class,
                $firstFormDto->typeMission->getId()
            );
        }
        if ($firstFormDto->categorie) {
            // Réattacher l'entité au contexte Doctrine si elle est détachée (vient de la session)
            $firstFormDto->categorie = $this->em->find(
                get_class($firstFormDto->categorie),
                $firstFormDto->categorie->getId()
            );
        }

        $dto->dateDemande = new DateTime('now');
        $dto->typeMission = $firstFormDto->typeMission;
        $dto->categorie = $firstFormDto->categorie;
        $dto->matricule = $firstFormDto->matricule;
        $dto->nom = $firstFormDto->salarier == 'TEMPORAIRE' ? $firstFormDto->nom : $user->getNom();
        $dto->prenom = $firstFormDto->salarier == 'TEMPORAIRE' ? $firstFormDto->prenom : $user->getPrenoms();
        $dto->cin = $firstFormDto->cin;
        $dto->salarier = $firstFormDto->salarier;
        $dto->indemniteForfaitaire = $this->getMontantIndemniteForfaitaire($firstFormDto, $user);

        $dto->rmq = $this->getRmq($user);
        $dto->site = $this->getSite($firstFormDto, $user);

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

    private function getRmq(User $user): Rmq
    {
        $agenceCode = $user->getAgenceUser()->getCode() ?? '';
        $codeToSearch = $agenceCode === (string)Agence::CODE_AGENCE_RENTAL ? (string)Agence::CODE_AGENCE_RENTAL : 'STD';

        return $this->em->getRepository(Rmq::class)->findOneBy(['description' => $codeToSearch]);
    }

    private function getSite(FirstFormDto $firstFormDto, User $user): Site
    {
        $criteria = [
            'sousTypeDocument' => $firstFormDto->typeMission,
            'rmq' => $this->getRmq($user),
            'categorie' => $firstFormDto->categorie
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

    private function getMontantIndemniteForfaitaire(FirstFormDto $firstFormDto, User $user): string
    {
        $criteria = [
            'sousTypeDocument' => $firstFormDto->typeMission,
            'rmq' => $this->getRmq($user),
            'categorie' => $firstFormDto->categorie,
            'site' => $this->getSite($firstFormDto, $user)
        ];

        $indemites = $this->em->getRepository(Indemnite::class)->findOneBy($criteria);
        if ($indemites) {
            $montant = $indemites->getMontant();

            $montant = $this->formattingService->formatNumber($montant, 0);
        } else {
            $montant = 0;
        }


        if ($firstFormDto->typeMission->getCodeSousType() === SousTypeDocument::CODE_TROP_PERCU) {
            $montant = 0;
        } else if ($firstFormDto->typeMission->getCodeSousType() === SousTypeDocument::CODE_COMPLEMENT || $firstFormDto->typeMission->getCodeSousType() === SousTypeDocument::CODE_MUTATION) {
            $montant  = '';
        }

        return $montant;
    }
}
