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

        // Helper pour récupérer la valeur (propriété directe ou getter)
        $getValue = function (string $property, $default = null) use ($data) {
            // Essayer d'abord la propriété directe
            if (isset($data->$property)) {
                return $data->$property;
            }
            // Essayer le getter
            $getter = 'get' . ucfirst($property);
            if (method_exists($data, $getter)) {
                return $data->$getter();
            }
            return $default;
        };

        // filtre sur l'ID du matériel
        $idMateriel = $getValue('idMateriel');
        if (!empty($idMateriel) && is_numeric($idMateriel)) {
            $id = (int) $idMateriel;
            $conditions[] = "mmat_nummat = $id";
        }

        // filtre sur le numéro de parc
        $numParc = $getValue('numParc');
        if (!empty($numParc) && is_string($numParc)) {
            $safeNumParc = trim(str_replace("'", "''", $numParc));
            $conditions[] = "trim(mmat_recalph) = '$safeNumParc'";
        }

        // filtre sur le numéro de série
        $numSerie = $getValue('numSerie');
        if (!empty($numSerie) && is_string($numSerie)) {
            $safeNumSerie = trim(str_replace("'", "''", $numSerie));
            $conditions[] = "trim(mmat_numserie) = '$safeNumSerie'";
        }

        return $conditions;
    }
}
