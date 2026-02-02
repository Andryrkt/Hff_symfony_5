<?php

namespace App\Model\Hf\Atelier\Dit;

use App\Model\DatabaseInformix;
use App\Model\Hf\Materiel\Traits\MaterielSearchTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DitModel
{
    use MaterielSearchTrait;

    private DatabaseInformix $databaseInformix;
    private ParameterBagInterface $parameters;

    public function __construct(
        DatabaseInformix $databaseInformix,
        ParameterBagInterface $parameters
    ) {
        $this->databaseInformix = $databaseInformix;
        $this->parameters = $parameters;
    }

    public function getHistoriqueMateriel(int $idMateriel, string $reparationRealise): array
    {
        $this->databaseInformix->connect();

        try {
            $estPneumatique = in_array($reparationRealise, ['ATE POL TANA']);
            $estPiece = in_array($reparationRealise, ['ATE TANA', 'ATE STAR', 'ATE MAS']);
            $constructeurPneumatique = $this->parameters->get('app.constructeurs.pneumatique') . ",'PNE'";
            $constructeurPiece = $this->parameters->get('app.constructeurs.pieces_magasin') . "," . $this->parameters->get('app.constructeurs.lub') . "," . $this->parameters->get('app.constructeurs.achat_locaux') . ",'SHE'";
            $conditionConstructeur = "";

            if ($estPneumatique) {
                $conditionConstructeur = "AND slor_constp IN ($constructeurPneumatique)";
            } else if ($estPiece) {
                $conditionConstructeur = "AND slor_constp IN ($constructeurPiece)";
            }

            $statement = " SELECT
                TRIM(seor_succ) AS codeAgence,
                TRIM(seor_servcrt) AS codeService,
                sitv_datdeb AS dateDebut,
                sitv_numor AS numeroOr, 
                sitv_interv AS numeroIntervention, 
                TRIM(sitv_comment) AS commentaire,
                sitv_pos AS pos,
                SUM(
                    slor_pxnreel * (
                    CASE 
                    WHEN slor_typlig = 'P' 
                        THEN (slor_qterel + slor_qterea + slor_qteres + slor_qtewait - slor_qrec) 
                    WHEN slor_typlig IN ('F','M','U','C') 
                        THEN slor_qterea 
                    END)
                ) AS somme
                FROM sav_eor, sav_lor, sav_itv, agr_succ, agr_tab ser, mat_mat, agr_tab ope, OUTER agr_tab sec
                WHERE seor_numor = slor_numor
                AND seor_serv <> 'DEV'
                AND sitv_numor = slor_numor
                AND sitv_interv = slor_nogrp/100
                AND (seor_succ = asuc_num)
                AND (seor_servcrt = ser.atab_code AND ser.atab_nom = 'SER')
                AND (sitv_typitv = sec.atab_code AND sec.atab_nom = 'TYI')
                AND (seor_ope = ope.atab_code AND ope.atab_nom = 'OPE')
                AND sitv_pos IN ('FC','FE','CP','ST', 'EC')
                AND (seor_nummat = mmat_nummat)
                AND mmat_nummat ='$idMateriel'
                $conditionConstructeur
                GROUP BY 1,2,3,4,5,6,7
                ORDER BY sitv_pos DESC, sitv_datdeb DESC, sitv_numor, sitv_interv
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows;
        } finally {
            $this->databaseInformix->close();
        }
    }

    public function getInfoMateriel(Object $data): array
    {
        $this->databaseInformix->connect();

        try {
            $conditions = $this->buildMaterielSearchConditions($data);

            $whereClause = !empty($conditions) ? ' AND (' . implode(' AND ', $conditions) . ')' : '';

            $statement = "SELECT

        mmat_marqmat as constructeur,
        trim(mmat_desi) as designation,
        trim(mmat_typmat) as modele,
        trim(mmat_numparc) as casier_emetteur,
        mmat_nummat as num_matricule,
        trim(mmat_numserie) as num_serie,
        trim(mmat_recalph) as num_parc,

        (select mhir_compteur from mat_hir a where a.mhir_nummat = mmat_nummat and a.mhir_daterel = (select max(b.mhir_daterel) from mat_hir b where b.mhir_nummat = a.mhir_nummat)) as heure,
        (select mhir_cumcomp from mat_hir a where a.mhir_nummat = mmat_nummat and a.mhir_daterel = (select max(b.mhir_daterel) from mat_hir b where b.mhir_nummat = a.mhir_nummat)) as km,
        (select nvl(sum(mofi_mt),0) from Informix.mat_ofi where mofi_classe = 30 and mofi_ssclasse in (10,11,12,13,14,16,17,18,19) and mofi_numbil = mbil_numbil and mofi_typmt = 'R') as cout_acquisition,
        (select nvl(sum(mofi_mt),0) from mat_ofi where mofi_classe = 30 and mofi_ssclasse = 15 and mofi_numbil = mbil_numbil and mofi_typmt = 'R') as amortissement,

        (select nvl(sum(mofi_mt),0) from mat_ofi where mofi_classe = 10 and mofi_ssclasse in (100,21,22,23) and mofi_numbil = mbil_numbil and mofi_typmt = 'R') as chiffre_affaires,
        (select nvl(sum(mofi_mt),0) from mat_ofi where mofi_classe = 40 and mofi_ssclasse in (100,110) and mofi_numbil = mbil_numbil and mofi_typmt = 'R') as charge_locative,
        (select nvl(sum(mofi_mt),0) from mat_ofi where mofi_classe = 40 and mofi_ssclasse in (21,22,23) and mofi_numbil = mbil_numbil and mofi_typmt = 'R') as charge_entretien
      
      FROM MAT_MAT
      LEFT JOIN mat_bil on mbil_nummat = mmat_nummat and mbil_dateclot <= '01/01/1900' and mbil_dateclot = '12/31/1899'
      WHERE MMAT_ETSTOCK in ('ST','AT', '--')
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
