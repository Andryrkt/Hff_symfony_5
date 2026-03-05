<?php

namespace App\Mapper\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsParInterventionDto;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;

class OrsParInterventionMapper
{
    private OrsModel $orsModel;

    public function __construct(OrsModel $orsModel)
    {
        $this->orsModel = $orsModel;
    }

    /**
     * Mappe les données Informix vers un tableau de DTOs d'interventions
     * 
     * @param OrsDto $orsDto
     * @return OrsParInterventionDto[]
     */
    public function mapToDtos(OrsDto $orsDto): array
    {
        $dtos = [];
        $infoSurLesOrs = $this->orsModel->getInfoOrs($orsDto->numeroDit, $orsDto->numeroOr);

        foreach ($infoSurLesOrs as $info) {
            $dto = new OrsParInterventionDto();
            $dto->numeroItv = (int) ($info['numeroItv'] ?? 0);
            $dto->nombreLigneItv = (int) ($info['nombreLigneItv'] ?? 0);
            $dto->montantItv = (float) ($info['montantItv'] ?? 0);
            $dto->montantPiece = (float) ($info['montantPiece'] ?? 0);
            $dto->montantMo = (float) ($info['montantMo'] ?? 0);
            $dto->montantAchatLocaux = (float) ($info['montantAchatLocaux'] ?? 0);
            $dto->montantFraisDivers = (float) ($info['montantFraisDivers'] ?? 0);
            $dto->montantLubrifiants = (float) ($info['montantLubrifiants'] ?? 0);
            $dto->libellelItv = (string) ($info['libellelItv'] ?? '');

            $dtos[] = $dto;
        }

        return $dtos;
    }
}
