<?php

namespace App\Model\Hf\Materiel\Badm;

use App\Model\DatabaseInformix;
use App\Dto\Hf\Materiel\Badm\FirstFormDto;
use App\Model\Hf\Materiel\Traits\MaterielSearchTrait;

class BadmModel
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
}
