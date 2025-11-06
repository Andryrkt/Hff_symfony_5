<?php

namespace App\Factory\Rh\Dom;

use DateTime;
use App\Entity\Rh\Dom\Site;
use App\Dto\Rh\Dom\FirstFormDto;
use App\Entity\Rh\Dom\Indemnite;
use App\Dto\Rh\Dom\SecondFormDto;
use App\Service\CodeExtractorService;
use App\Entity\Admin\PersonnelUser\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\PersonnelUser\Personnel;
use Symfony\Component\Security\Core\Security;
use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use App\Entity\Rh\Dom\Rmq;
use App\Entity\Rh\Dom\SousTypeDocument;
use App\Service\Utils\FormattingService;

class SecondFormDtoFactory
{
    private $security;
    private $em;
    private FormattingService $formattingService;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        FormattingService $formattingService
    ) {
        $this->security = $security;
        $this->em = $em;
        $this->formattingService = $formattingService;
    }

    public function create(FirstFormDto $firstFormDto): SecondFormDto
    {
        $dto = new SecondFormDto();
        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
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
        $dto->agenceUser = $agence->getCode() . ' ' . $agence->getNom();
        $dto->serviceUser = $service->getCode() . ' ' . $service->getNom();
        $dto->debiteur = ['agence' => $agence, 'service' => $service];

        return $dto;
    }

    private function getRmq(User $user): Rmq
    {
        $agenceCode = $user->getAgenceUser()->getCode() ?? '';
        $codeToSearch = $agenceCode === Agence::CODE_AGENCE_RENTAL ? Agence::CODE_AGENCE_RENTAL : 'STD';

        return $this->em->getRepository(Rmq::class)->findOneBy(['description' => $codeToSearch]);
    }

    private function getSite(FirstFormDto $firstFormDto, User $user): Site
    {
        $criteria = [
            'sousTypeDocumentId' => $firstFormDto->typeMission,
            'rmqId' => $this->getRmq($user),
            'categorieId' => $firstFormDto->categorie
        ];

        $indemites = $this->em->getRepository(Indemnite::class)->findBy($criteria);
        foreach ($indemites as $value) {
            $sites[] = $value->getSiteId()->getNomZone();
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
            'sousTypeDocumentId' => $firstFormDto->typeMission,
            'rmqId' => $this->getRmq($user),
            'categorieId' => $firstFormDto->categorie,
            'siteId' => $this->getSite($firstFormDto, $user)
        ];

        $indemites = $this->em->getRepository(Indemnite::class)->findOneBy($criteria);
        if ($indemites) {
            $montant = $indemites->getMontant();

            $montant = $this->formattingService->formatNumber($montant);
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
