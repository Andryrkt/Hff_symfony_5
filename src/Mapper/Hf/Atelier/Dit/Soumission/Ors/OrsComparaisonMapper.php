<?php

namespace App\Mapper\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsComparaisonItvDto;
use App\Entity\Hf\Atelier\Dit\Soumission\Ors\Ors;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;

class OrsComparaisonMapper
{
    private OrsModel $orsModel;

    public function __construct(OrsModel $orsModel)
    {
        $this->orsModel = $orsModel;
    }

    /**
     * @param Ors[] $vmsAv
     * @param Ors[] $vmsAp
     * @param array $datePlanningMap Un tableau associatif [numeroItv => \DateTime]
     * @return OrsComparaisonItvDto[]
     */
    public function mapToComparaisonDtos(array $vmsAv, array $vmsAp, array $datePlanningMap = []): array
    {
        if (!empty($vmsAv)) {
            $manquantsAp = $this->objetsManquantsParNumero($vmsAv, $vmsAp);
            $manquantsAv = $this->objetsManquantsParNumero($vmsAp, $vmsAv);

            $vmsAv = array_merge($vmsAv, $manquantsAp);
            $vmsAp = array_merge($vmsAp, $manquantsAv);

            $this->trierTableauParNumero($vmsAv);
            $this->trierTableauParNumero($vmsAp);
        }

        $recap = [];

        foreach ($vmsAp as $index => $vmAp) {
            $vmAv = $vmsAv[$index] ?? null;

            $dto = new OrsComparaisonItvDto();
            $dto->itv = $vmAp->getNumeroItv();
            $dto->libelleItv = $vmAp->getLibellelItv() ?? ($vmAv ? $vmAv->getLibellelItv() : '');

            // Utilisation de la map si disponible, sinon repli sur l'ancienne méthode
            if (isset($datePlanningMap[$dto->itv])) {
                $dto->datePlanning = $datePlanningMap[$dto->itv];
            } else {
                $dto->datePlanning = $this->datePlanning($vmAp->getNumeroOr(), $dto->itv);
            }

            // Données APRES
            $dto->nbLigAp = $vmAp->getNombreLigneItv() ?: 0;
            $dto->mttTotalAp = (float) ($vmAp->getMontantItv() ?: 0);

            // Données AVANT
            if ($vmAv) {
                $dto->nbLigAv = $vmAv->getNombreLigneItv() ?: 0;
                $dto->mttTotalAv = (float) ($vmAv->getMontantItv() ?: 0);
            }

            // Calcul du statut
            $this->calculerStatut($dto);

            $recap[] = $dto;
        }

        return $recap;
    }

    private function calculerStatut(OrsComparaisonItvDto $dto): void
    {
        if ($dto->mttTotalAv == 0 && $dto->mttTotalAp > 0) {
            $dto->statut = 'Nouv';
        } elseif ($dto->mttTotalAp == 0 && $dto->mttTotalAv > 0) {
            $dto->statut = 'Supp';
        } elseif ($dto->mttTotalAv != $dto->mttTotalAp || $dto->nbLigAv != $dto->nbLigAp) {
            $dto->statut = 'Modif';
        }
    }

    private function objetsManquantsParNumero(array $cible, array $source): array
    {
        $manquants = [];
        $itvsCible = array_map(fn($o) => $o->getNumeroItv(), $cible);

        foreach ($source as $objetSource) {
            if (!in_array($objetSource->getNumeroItv(), $itvsCible)) {
                $fantome = new Ors();
                $fantome->setNumeroOr($objetSource->getNumeroOr());
                $fantome->setNumeroItv($objetSource->getNumeroItv());
                $fantome->setLibellelItv($objetSource->getLibellelItv());
                $fantome->setNombreLigneItv(0);
                $fantome->setMontantItv(0);

                $manquants[] = $fantome;
            }
        }

        return $manquants;
    }

    private function trierTableauParNumero(array &$tableau): void
    {
        usort($tableau, fn($a, $b) => $a->getNumeroItv() <=> $b->getNumeroItv());
    }

    private function datePlanning(int $numeroOr, int $numeroItv): ?\DateTime
    {
        return $this->orsModel->getDatePlanning($numeroOr, $numeroItv);
    }
}
