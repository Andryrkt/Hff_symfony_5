<?php

namespace App\Service\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;

/**
 * Service dédié au calcul des agrégats pour les documents PDF OR
 */
class OrsPdfCalculator
{
    /**
     * Calcule le pied de page pour le tableau de situation de l'OR
     */
    public function getFooterSituationOr(OrsDto $dto): array
    {
        $totalNbLigAv = 0;
        $totalNbLigAp = 0;
        $totalMttTotalAv = 0.0;
        $totalMttTotalAp = 0.0;

        foreach ($dto->orsApresDtos as $itv) {
            $totalNbLigAv += $itv->nbLigAv;
            $totalNbLigAp += $itv->nbLigAp;
            $totalMttTotalAv += $itv->mttTotalAv;
            $totalMttTotalAp += $itv->mttTotalAp;
        }

        return [
            'itv'              => '',
            'libelleItv'       => '',
            'datePlanning'     => 'TOTAL',
            'nbLigAv'          => $totalNbLigAv,
            'nbLigAp'          => $totalNbLigAp,
            'mttTotalAv'       => $totalMttTotalAv,
            'mttTotalAp'       => $totalMttTotalAp,
            'statut'           => ''
        ];
    }

    /**
     * Calcule les statistiques de contrôle (différences avant/après)
     */
    public function getStatsControle(OrsDto $dto): array
    {
        $stats = ['nbrNouv' => 0, 'nbrSupp' => 0, 'nbrModif' => 0, 'mttModif' => 0.0];
        foreach ($dto->orsApresDtos as $compDto) {
            if ($compDto->statut === 'Nouv') {
                $stats['nbrNouv']++;
            } elseif ($compDto->statut === 'Supp') {
                $stats['nbrSupp']++;
            } elseif ($compDto->statut === 'Modif') {
                $stats['nbrModif']++;
                $stats['mttModif'] += abs($compDto->mttTotalAp - $compDto->mttTotalAv);
            }
        }
        return $stats;
    }

    /**
     * Calcule le pied de page pour le tableau de récapitulation
     */
    public function getFooterRecapitulationOR(OrsDto $dto): array
    {
        $montantItv = 0;
        $montantPiece = 0;
        $montantMo = 0.0;
        $montantAchatLocaux = 0.0;
        $montantLubrifiants = 0.0;
        $montantFraisDivers = 0.0;

        foreach ($dto->orsParInterventionDtos as $itv) {
            $montantItv += $itv->montantItv;
            $montantPiece += $itv->montantPiece;
            $montantMo += $itv->montantMo;
            $montantAchatLocaux += $itv->montantAchatLocaux;
            $montantLubrifiants += $itv->montantLubrifiants;
            $montantFraisDivers += $itv->montantFraisDivers;
        }

        return [
            'numeroItv'          => 'TOTAL',
            'montantItv'         => $montantItv,
            'montantPiece'       => $montantPiece,
            'montantMo'          => $montantMo,
            'montantAchatLocaux' => $montantAchatLocaux,
            'montantLubrifiants' => $montantLubrifiants,
            'montantFraisDivers' => $montantFraisDivers
        ];
    }
}
