<?php

namespace App\Factory\Hf\Materiel\Casier;


use App\Entity\Hf\Materiel\Casier\Casier;
use App\Dto\Hf\Materiel\Casier\SecondFormDto;
use App\Service\Utils\NumeroGeneratorService;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use App\Constants\Admin\Historisation\TypeDocumentConstants;


class CasierFactory
{
    private NumeroGeneratorService $numeroGeneratorService;
    private StatutDemandeRepository $statutDemandeRepository;

    public function __construct(
        NumeroGeneratorService $numeroGeneratorService,
        StatutDemandeRepository $statutDemandeRepository
    ) {
        $this->numeroGeneratorService = $numeroGeneratorService;
        $this->statutDemandeRepository = $statutDemandeRepository;
    }

    public function create(SecondFormDto $secondFormDto): Casier
    {
        $casier = new Casier();
        $casier->setNom($secondFormDto->client . ' - ' . $secondFormDto->chantier);
        $casier->setAgenceRattacher($secondFormDto->agenceRattacher);
        $casier->setNumero($this->numeroGeneratorService->autoGenerateNumero(TypeDocumentConstants::TYPE_DOCUMENT_CAS_CODE, true));
        $casier->setStatutDemande($this->statutDemandeRepository->findOneBy(['codeApplication' => TypeDocumentConstants::TYPE_DOCUMENT_CAS_CODE, 'description' => 'ATTENTE VALIDATION']));

        return $casier;
    }
}
