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


    public function getInfoMateriel(FirstFormDto $data): array
    {
        $this->databaseInformix->connect();

        try {
            $conditions = $this->buildMaterielSearchConditions($data);

            if (empty($conditions)) {
                return [];
            }

            $whereClause = ' AND (' . implode(' AND ', $conditions) . ')';

            $statement = "SELECT
                            case  when mmat_succ in (select asuc_parc from Informix.agr_succ) then asuc_num else mmat_succ end as code_agence,
                            trim(asuc_lib)||'-'||case (select sce.atab_lib from informix.mmo_imm, Informix.agr_tab as sce where mimm_soc = mmat_soc and mimm_nummat = mmat_nummat and sce.atab_code = mimm_service and sce.atab_nom='SER') when null then 'COMMERCIAL' 
                            else(select sce.atab_lib from Informix.mmo_imm, Informix.agr_tab as sce where mimm_soc = 'HF' and mimm_nummat = mmat_nummat and sce.atab_code = mimm_service and sce.atab_nom='SER')
                            end as service,
                            case (select mimm_service  from Informix.mmo_imm where mimm_soc = mmat_soc and mimm_nummat = mmat_nummat) when null then 'LCD' 
                            else(select mimm_service  from Informix.mmo_imm where mimm_soc = mmat_soc and mimm_nummat = mmat_nummat)
                            end as code_service,
                            trim((select atab_lib from Informix.agr_tab where atab_code = mmat_etstock and atab_nom = 'ETM')) as groupe1,
                            trim((select atab_lib from Informix.agr_tab where atab_code = mmat_affect and atab_nom = 'AFF')) as affectation,
                            mmat_marqmat as constructeur,
                            trim(mmat_desi) as designation,
                            trim(mmat_typmat) as modele,
                            mmat_nummat as num_matricule,
                            trim(mmat_numserie) as num_serie,
                            trim(mmat_recalph) as num_parc ,
                            (select mhir_compteur from Informix.mat_hir a where a.mhir_nummat = mmat_nummat and a.mhir_daterel = (select max(b.mhir_daterel) from Informix.mat_hir b where b.mhir_nummat = a.mhir_nummat)) as heure_machine,
                            (select mhir_cumcomp from Informix.mat_hir a where a.mhir_nummat = mmat_nummat and a.mhir_daterel = (select max(b.mhir_daterel) from Informix.mat_hir b where b.mhir_nummat = a.mhir_nummat)) as km_machine,
                            (select mhir_daterel from Informix.mat_hir a where a.mhir_nummat = mmat_nummat and a.mhir_daterel = (select max(b.mhir_daterel) from Informix.mat_hir b where b.mhir_nummat = a.mhir_nummat)) as Date_compteur,
                            trim(mmat_numparc) as casier_emetteur,
                            year(mmat_datemser) as annee_du_modele,
                            date(mmat_datentr) as date_achat,
                            (select nvl(sum(mofi_mt),0) from Informix.mat_ofi where mofi_classe = 30 and mofi_ssclasse in (10,11,12,13,14,16,17,18,19) and mofi_numbil = mbil_numbil and mofi_typmt = 'R' and mofi_lib like 'Prix d''achat') as Prix_achat,
                            (select nvl(sum(mofi_mt),0) from Informix.mat_ofi where mofi_classe = 30 and mofi_ssclasse = 15 and mofi_numbil = mbil_numbil and mofi_typmt = 'R') as amortissement,
                            (select nvl(sum(mofi_mt),0) from Informix.mat_ofi where mofi_classe = 40 and mofi_ssclasse in (21,22,23) and mofi_numbil = mbil_numbil and mofi_typmt = 'R') as charge_entretien,
                            (select nvl(sum(mofi_mt),0) from Informix.mat_ofi where mofi_classe = 30 and mofi_ssclasse in (10,11,12,13,14,16,17,18,19) and mofi_numbil = mbil_numbil and mofi_typmt = 'R') as cout_acquisition, -- droits taxe
                            mmat_nouo as etat_achat,
                            trim((select atab_lib from Informix.agr_tab where atab_code = mmat_natmat and atab_nom = 'NAT')) as famille,
                            trim(mmat_affect) as code_affect,
                            (select  mimm_dateserv from Informix.mmo_imm where mimm_nummat = mmat_nummat) as date_mise_en_location
                        FROM Informix.mat_mat, Informix.agr_succ, outer mat_bil
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
            $rows = $this->databaseInformix->fetchScalarResults($result);

            return $rows;
        } finally {
            $this->databaseInformix->close();
        }
    }
}
