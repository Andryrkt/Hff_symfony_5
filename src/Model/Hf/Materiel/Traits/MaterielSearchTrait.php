<?php

namespace App\Model\Hf\Materiel\Traits;

trait MaterielSearchTrait
{
    /**
     * Construit les conditions de recherche pour le matériel
     * 
     * @param object $data DTO contenant les filtres (idMateriel, numParc, numSerie)
     * @return array Tableau de strings contenant les clauses WHERE
     */
    public function buildMaterielSearchConditions(object $data): array
    {
        $conditions = [];

        // filtre sur l'ID du matériel
        if (property_exists($data, 'idMateriel') && !empty($data->idMateriel) && is_numeric($data->idMateriel)) {
            $id = (int) $data->idMateriel;
            $conditions[] = "mmat_nummat = $id";
        }

        // filtre sur le numéro de parc
        if (property_exists($data, 'numParc') && !empty($data->numParc) && is_string($data->numParc)) {
            $safeNumParc = trim(str_replace("'", "''", $data->numParc));
            $conditions[] = "trim(mmat_recalph) = '$safeNumParc'";
        }

        // filtre sur le numéro de série
        if (property_exists($data, 'numSerie') && !empty($data->numSerie) && is_string($data->numSerie)) {
            $safeNumSerie = trim(str_replace("'", "''", $data->numSerie));
            $conditions[] = "trim(mmat_numserie) = '$safeNumSerie'";
        }

        return $conditions;
    }
}
