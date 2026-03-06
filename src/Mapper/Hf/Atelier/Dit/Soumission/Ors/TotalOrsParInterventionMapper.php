<?php

namespace App\Mapper\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsParInterventionDto;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\TotalOrsParInterventionDto;

class TotalOrsParInterventionMapper
{
    /**
     * Calcule les totaux à partir des DTOs d'interventions
     * 
     * @param OrsParInterventionDto[] $interventions
     * @return TotalOrsParInterventionDto
     */
    public function calculateTotals(OrsDto $orsDto): TotalOrsParInterventionDto
    {
        $totalDto = new TotalOrsParInterventionDto();

        foreach ($orsDto->orsParInterventionDtos as $orsParInterventionDto) {
            $totalDto->montantItv += $orsParInterventionDto->montantItv;
            $totalDto->montantPiece += $orsParInterventionDto->montantPiece;
            $totalDto->montantMo += $orsParInterventionDto->montantMo;
            $totalDto->montantAchatLocaux += $orsParInterventionDto->montantAchatLocaux;
            $totalDto->montantFraisDivers += $orsParInterventionDto->montantFraisDivers;
            $totalDto->montantLubrifiants += $orsParInterventionDto->montantLubrifiants;
        }

        return $totalDto;
    }
}
