<?php

namespace App\Mapper\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsComparaisonItvDto;
use App\Entity\Hf\Atelier\Dit\Soumission\Ors\Ors;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;
use App\Service\Utils\CollectionComparatorService;

class OrsComparaisonMapper
{
    private OrsModel $orsModel;
    private CollectionComparatorService $comparator;

    public function __construct(OrsModel $orsModel, CollectionComparatorService $comparator)
    {
        $this->orsModel = $orsModel;
        $this->comparator = $comparator;
    }

    /**
     * @param Ors[] $vmsAv
     * @param Ors[] $vmsAp
     * @param array $datePlanningMap Un tableau associatif [numeroItv => \DateTime]
     * @return OrsComparaisonItvDto[]
     */
    public function mapToComparaisonDtos(array $vmsAv, array $vmsAp, array $datePlanningMap = []): array
    {
        // On définit ce qui constitue une "modification"
        $isModified = fn($av, $ap) =>
        (float) $av->getMontantItv() != (float) $ap->getMontantItv() ||
            (int) $av->getNombreLigneItv() != (int) $ap->getNombreLigneItv();

        $comparison = $this->comparator->compare($vmsAv, $vmsAp, fn($o) => $o->getNumeroItv(), $isModified);

        $recap = [];

        foreach ($comparison as $id => $data) {
            $orsAv = $data['before'];
            $orsAp = $data['after'];

            $dto = new OrsComparaisonItvDto();
            $dto->itv = (int) $id;
            $dto->statut = $data['status'];

            // On prend le libellé de la version APRES, sinon AVANT
            $dto->libelleItv = ($orsAp ? $orsAp->getLibellelItv() : null) ?? ($orsAv ? $orsAv->getLibellelItv() : '');

            // Utilisation de la map si disponible, sinon null
            if (isset($datePlanningMap[$dto->itv])) {
                $dto->datePlanning = $datePlanningMap[$dto->itv];
            } else {
                $dto->datePlanning = null;
            }

            // Données AVANT
            if ($orsAv) {
                $dto->nbLigAv = $orsAv->getNombreLigneItv() ?: 0;
                $dto->mttTotalAv = (float) ($orsAv->getMontantItv() ?: 0);
            }

            // Données APRES
            if ($orsAp) {
                $dto->nbLigAp = $orsAp->getNombreLigneItv() ?: 0;
                $dto->mttTotalAp = (float) ($orsAp->getMontantItv() ?: 0);
            }

            $recap[] = $dto;
        }

        return $recap;
    }
}
