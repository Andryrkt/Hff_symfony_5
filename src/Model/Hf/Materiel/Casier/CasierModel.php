<?php

namespace App\Model\Hf\Materiel\Casier;

use App\Model\DatabaseInformix;
use App\Dto\Hf\Materiel\Casier\FirstFormDto;
use App\Model\Hf\Materiel\Traits\MaterielSearchTrait;

final class CasierModel
{
    use MaterielSearchTrait;

    private DatabaseInformix $databaseInformix;

    public function __construct(
        DatabaseInformix $databaseInformix
    ) {
        $this->databaseInformix = $databaseInformix;
    }

    /**
     * Vérification de l'existence du matériel
     */
    public function estMaterielExiste(FirstFormDto $data): bool
    {
        $this->databaseInformix->connect();

        try {
            $conditions = $this->buildMaterielSearchConditions($data);

            if (empty($conditions)) {
                return false;
            }

            $whereClause = ' AND (' . implode(' AND ', $conditions) . ')';

            $statement = "SELECT COUNT(mmat_nummat) as count_matricule
                    FROM informix.mat_mat, informix.agr_succ, outer mat_bil
                    WHERE (MMAT_SUCC in ('01', '02', '20', '30', '40', '50', '60', '80', '90','91','92') or MMAT_SUCC IN (SELECT ASUC_PARC FROM informix.AGR_SUCC WHERE ASUC_NUM IN ('01','02', '20', '30', '40', '50', '60', '80', '90','91','92') ))
                    and trim(MMAT_ETSTOCK) in ('ST','AT')
                    and trim(MMAT_AFFECT) in ('IMM','VTE','LCD','SDO')
                    and mmat_soc = 'HF'
                    and (mmat_succ = asuc_num or mmat_succ = asuc_parc)
                    and mmat_nummat = mbil_nummat
                    and mbil_dateclot = MDY(12, 31, 1899)
                    and mmat_datedisp < MDY(12, 31, 2999)
                    and (MMAT_ETACHAT = 'FA' and MMAT_ETVENTE = '--')
                    $whereClause
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return isset($rows[0]['count_matricule']) && $rows[0]['count_matricule'] > 0;
        } finally {
            $this->databaseInformix->close();
        }
    }


    /**
     * Récupération des informations du matériel
     */
    public function getCaracteristiqueMateriel(FirstFormDto $data): array
    {
        $this->databaseInformix->connect();

        try {
            $conditions = $this->buildMaterielSearchConditions($data);

            if (empty($conditions)) {
                return [];
            }

            $whereClause = ' AND (' . implode(' AND ', $conditions) . ')';

            $statement = "SELECT 
                            trim(mmat_desi) as designation,
                            mmat_nummat as num_matricule,
                            trim(mmat_numserie) as num_serie,
                            trim(mmat_recalph) as num_parc ,
                            trim((select atab_lib from agr_tab where atab_code = mmat_natmat and atab_nom = 'NAT')) as famille, -- ou groupe
                            mmat_marqmat as constructeur,
                            trim(mmat_typmat) as modele,
                            year(mmat_datemser) as annee_du_modele,
                            trim((select atab_lib from agr_tab where atab_code = mmat_affect and atab_nom = 'AFF')) as affectation,
                            date(mmat_datentr) as date_achat
                    FROM informix.mat_mat, informix.agr_succ, outer mat_bil
                    WHERE (MMAT_SUCC in ('01', '02', '20', '30', '40', '50', '60', '80', '90','91','92') or MMAT_SUCC IN (SELECT ASUC_PARC FROM informix.AGR_SUCC WHERE ASUC_NUM IN ('01','02', '20', '30', '40', '50', '60', '80', '90','91','92') ))
                    and trim(MMAT_ETSTOCK) in ('ST','AT')
                    and trim(MMAT_AFFECT) in ('IMM','VTE','LCD','SDO')
                    and mmat_soc = 'HF'
                    and (mmat_succ = asuc_num or mmat_succ = asuc_parc)
                    and mmat_nummat = mbil_nummat
                    and mbil_dateclot = MDY(12, 31, 1899)
                    and mmat_datedisp < MDY(12, 31, 2999)
                    and (MMAT_ETACHAT = 'FA' and MMAT_ETVENTE = '--')
                    $whereClause
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows;
        } finally {
            $this->databaseInformix->close();
        }
    }
}
