<?php

namespace App\Factory\Hf\Atelier\Dit\Soumission\Ors;

use App\Constants\Hf\Atelier\Dit\Soumission\Ors\StatutOrConstant;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Mapper\Hf\Atelier\Dit\Soumission\Ors\OrsParInterventionMapper;
use App\Repository\Hf\Atelier\Dit\Ors\OrsRepository;
use App\Service\Utils\NumeroGeneratorService;

class OrsFactory
{
    private OrsRepository $orsRepository;
    private NumeroGeneratorService $numeroGeneratorService;
    private OrsParInterventionMapper $orsParInterventionMapper;

    public function __construct(
        OrsRepository $orsRepository,
        NumeroGeneratorService $numeroGeneratorService,
        OrsParInterventionMapper $orsParInterventionMapper
    ) {
        $this->orsRepository = $orsRepository;
        $this->numeroGeneratorService = $numeroGeneratorService;
        $this->orsParInterventionMapper = $orsParInterventionMapper;
    }

    public function create(string $numeroDit, string $numeroOr): OrsDto
    {
        $dto = new OrsDto();

        $dto->numeroDit = $numeroDit;
        $dto->numeroOr = $numeroOr;

        return $dto;
    }

    public function enrichissementDto(OrsDto $dto)
    {
        $dto->numeroVersion = $this->getNumeroVersion($dto);
        $dto->statut = StatutOrConstant::SOUMIS_A_VALIDATION;

        // Récupération automatique des interventions liées
        $dto->orsParInterventionDtos = $this->orsParInterventionMapper->mapToDtos($dto);
    }

    private function getNumeroVersion(OrsDto $dto)
    {
        $numeroVersion = $this->orsRepository->getNumeroVersion($dto->numeroOr);
        return $this->numeroGeneratorService->simpleIncrement($numeroVersion);
    }
}
