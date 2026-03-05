<?php

namespace App\Factory\Hf\Atelier\Dit\Soumission\Ors;

use App\Constants\Hf\Atelier\Dit\Soumission\Ors\StatutOrConstant;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Mapper\Hf\Atelier\Dit\Soumission\Ors\OrsParInterventionMapper;
use App\Mapper\Hf\Atelier\Dit\Soumission\Ors\PieceFaibleAchatMapper;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;
use App\Repository\Hf\Atelier\Dit\Ors\OrsRepository;
use App\Service\Utils\NumeroGeneratorService;

class OrsFactory
{
    private OrsRepository $orsRepository;
    private NumeroGeneratorService $numeroGeneratorService;
    private OrsParInterventionMapper $orsParInterventionMapper;
    private PieceFaibleAchatMapper $pieceFaibleAchatMapper;
    private OrsModel $orsModel;

    public function __construct(
        OrsRepository $orsRepository,
        NumeroGeneratorService $numeroGeneratorService,
        OrsParInterventionMapper $orsParInterventionMapper,
        PieceFaibleAchatMapper $pieceFaibleAchatMapper,
        OrsModel $orsModel
    ) {
        $this->orsRepository = $orsRepository;
        $this->numeroGeneratorService = $numeroGeneratorService;
        $this->orsParInterventionMapper = $orsParInterventionMapper;
        $this->pieceFaibleAchatMapper = $pieceFaibleAchatMapper;
        $this->orsModel = $orsModel;
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
        $dto->dateDemande = new \DateTime();

        $dto->numeroDevis = $this->getNumeroDevis($dto);
        $dto->estPieceSortieMagasin = $this->getPieceSortieMagasin($dto);
        $dto->estPieceAchatLocaux = $this->getPieceAchatLocaux($dto);
        $dto->estPiecePol = $this->getPiecePol($dto);

        // Récupération automatique par ligne d'interventions de l'OR (Récapitulation de l'OR)
        $dto->orsParInterventionDtos = $this->orsParInterventionMapper->mapToDtos($dto);
        // Récupération automatique des pièces faible achat
        $dto->pieceFaibleAchatDtos = $this->pieceFaibleAchatMapper->mapToDtos($dto);
    }

    private function getNumeroVersion(OrsDto $dto)
    {
        $numeroVersion = $this->orsRepository->getNumeroVersion($dto->numeroOr);
        return $this->numeroGeneratorService->simpleIncrement($numeroVersion);
    }

    private function getNumeroDevis(OrsDto $dto)
    {
        return $this->orsModel->getNumeroDevis($dto->numeroOr);
    }

    private function getPieceSortieMagasin(OrsDto $dto)
    {
        return $this->orsModel->getPieceSortieMagasin($dto->numeroOr);
    }

    private function getPieceAchatLocaux(OrsDto $dto)
    {
        return $this->orsModel->getPieceAchatLocaux($dto->numeroOr);
    }

    private function getPiecePol(OrsDto $dto)
    {
        return $this->orsModel->getPiecePol($dto->numeroOr);
    }
}
