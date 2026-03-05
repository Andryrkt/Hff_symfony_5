<?php

namespace App\Mapper\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Entity\Hf\Atelier\Dit\Soumission\Ors\Ors;

class OrsMapper
{

    public function map(OrsDto $orsDto): array
    {
        $ors = [];

        foreach ($orsDto->orsParInterventionDtos as $orsParInterventionDto) {
            $or = new Ors();
            $or
                ->setNumeroOr($orsDto->numeroOr)
                ->setNumeroDit($orsDto->numeroDit)
                ->setNumeroVersion($orsDto->numeroVersion)
                ->setStatut($orsDto->statut)
                ->setPieceFaibleActiviteAchat($orsDto->pieceFaibleActiviteAchat)
                ->setNumeroItv($orsParInterventionDto->numeroItv)
                ->setNombreLigneItv($orsParInterventionDto->nombreLigneItv)
                ->setMontantItv($orsParInterventionDto->montantItv)
                ->setMontantPiece($orsParInterventionDto->montantPiece)
                ->setMontantMo($orsParInterventionDto->montantMo)
                ->setMontantAchatLocaux($orsParInterventionDto->montantAchatLocaux)
                ->setMontantFraisDivers($orsParInterventionDto->montantFraisDivers)
                ->setMontantLubrifiants($orsParInterventionDto->montantLubrifiants)
                ->setLibellelItv($orsParInterventionDto->libellelItv);

            $ors[] = $or;
        }

        return $ors;
    }
}
