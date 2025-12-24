<?php

namespace App\Model\Hf\Atelier\Planning;

use App\Dto\Hf\Atelier\Planning\PlanningSearchDto;
use App\Model\DatabaseInformix;
use App\Service\Traits\ArrayHelperTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class PlanningModel
{
    use ArrayHelperTrait;

    private DatabaseInformix $databaseInformix;
    private ParameterBagInterface $parameters;

    public function __construct(
        DatabaseInformix $databaseInformix,
        ParameterBagInterface $parameters
    ) {
        $this->databaseInformix = $databaseInformix;
        $this->parameters = $parameters;
    }

    /**
     * Recupération des interventions en back order
     */
    public function getBackOrderPlanning(string $lesOrValideString, string $tousLesOrSoumisString, PlanningSearchDto $planningSearchDto): string
    {
        $this->databaseInformix->connect();

        try {
            if (!empty($lesOrValideString)) {
                if ($planningSearchDto->orNonValiderDw) {
                    $vOrvalDw = " AND slor_numor not in ($tousLesOrSoumisString) ";
                } else {
                    $vOrvalDw = " AND slor_numor in ($lesOrValideString) ";
                }
            } else {
                $vOrvalDw = "";
            }

            $statement = " SELECT distinct 
                   CAST(sav.slor_numor || '-' || trunc(sav.slor_nogrp/100) as varchar(50))AS intervention
                  FROM sav_lor AS sav
                  INNER JOIN gcot_acknow_cat AS cat
                  ON CAST(sav.slor_numcf  as varchar(50))= CAST(cat.numero_po as varchar(50))
                  AND (sav.slor_nolign = cat.line_number OR  sav.slor_noligncm = cat.line_number)
                  AND sav.slor_refp = cat.parts_number
                  WHERE (  CAST(cat.libelle_type as varchar(10))= 'Error'  or CAST(cat.libelle_type as varchar(10))= 'Back Order'  ) 
                  AND cat.id_gcot_acknow_cat = (
                                              SELECT MAX(sub.id_gcot_acknow_cat )
                                              FROM gcot_acknow_cat AS sub
                                              WHERE sub.parts_number = cat.parts_number
                                                AND sub.numero_po = cat.numero_po
                                                AND sub.line_number = cat.line_number
                                          )
                    $vOrvalDw
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $this->TableauEnString($rows);
        } finally {
            $this->databaseInformix->close();
        }
    }

    /**
     * Recupération des information de matériel à planifier
     */
    public function getInformationMaterielPlannifier(PlanningSearchDto $planningSearchDto, string $touslesOrItvSoumisString, string $lesOrValideString, string $backOrderString): array
    {
        $this->databaseInformix->connect();

        try {

            if ($planningSearchDto->orBackOrder) {
                $vOrvalDw = "AND seor_numor ||'-'||sitv_interv in ($backOrderString) ";
            } else {
                if (!empty($lesOrValideString)) {
                    if ($planningSearchDto->orNonValiderDw) {
                        $vOrvalDw = " AND seor_numor ||'-'||sitv_interv not in ($touslesOrItvSoumisString) ";
                    } else {
                        $vOrvalDw = " AND seor_numor ||'-'||sitv_interv in ($lesOrValideString) ";
                    }
                } else {
                    $vOrvalDw = " ";
                }
            }


            $vligneType = $this->typeLigne($planningSearchDto);

            $vYearsStatutPlan =  $this->planAnnee($planningSearchDto);
            $vConditionNoPlanning = $this->nonplannfierSansDatePla($planningSearchDto);
            $vMonthStatutPlan = $this->planMonth($planningSearchDto);
            $vDateDMonthPlan = $this->dateDebutMonthPlan($planningSearchDto);
            $vDateFMonthPlan = $this->dateFinMonthPlan($planningSearchDto);
            $vStatutFacture = $this->facture($planningSearchDto);
            $agence = $this->agence($planningSearchDto);
            $vStatutInterneExterne = $this->interneExterne($planningSearchDto);
            $agenceDebite = $this->agenceDebite($planningSearchDto);
            $serviceDebite = $this->serviceDebite($planningSearchDto);
            $vconditionNumParc = $this->numParc($planningSearchDto);
            $vconditionIdMat = $this->idMat($planningSearchDto);
            $vconditionNumOr = $this->numOr($planningSearchDto);
            $vconditionNumSerie = $this->numSerie($planningSearchDto);
            $vconditionCasier = $this->casier($planningSearchDto);
            $vsection = $this->section($planningSearchDto);

            $statement = " SELECT
                      trim(seor_succ) as codeSuc, 
                      trim(asuc_lib) as libSuc, 
                      trim(seor_servcrt) as codeServ, 
                      trim(ser.atab_lib) as libServ, 
                      trim(sitv_comment) as commentaire,
                      mmat_nummat as idMat,
                      trim(mmat_marqmat) as markMat,
                      trim(mmat_typmat) as typeMat ,
                      trim(mmat_numserie) as numSerie,
                      trim(mmat_recalph) as numParc,
                      trim(mmat_numparc) as casier,
                      $vYearsStatutPlan as annee,
                      $vMonthStatutPlan as mois,
                      seor_numor ||'-'||sitv_interv as orIntv,

                      (  SELECT SUM( CASE WHEN slor_typlig = 'P' $vligneType  THEN
                                                slor_qterel + slor_qterea + slor_qteres + slor_qtewait - slor_qrec
                                          ELSE slor_qterea END )
                        FROM sav_lor as A  , sav_itv  AS B WHERE  A.slor_numor = B.sitv_numor AND  B.sitv_interv = A.slor_nogrp/100 AND A.slor_numor = C.slor_numor and B.sitv_interv  = D.sitv_interv  $vligneType ) as QteCdm,
                    	(  SELECT SUM(slor_qterea ) FROM sav_lor as A  , sav_itv  AS B WHERE  A.slor_numor = B.sitv_numor AND  B.sitv_interv = A.slor_nogrp/100 AND A.slor_numor = C.slor_numor and B.sitv_interv  = D.sitv_interv  $vligneType ) as QtLiv,
                      (  SELECT SUM(slor_qteres )FROM sav_lor as A  , sav_itv  AS B WHERE  A.slor_numor = B.sitv_numor AND  B.sitv_interv = A.slor_nogrp/100 AND A.slor_numor = C.slor_numor and B.sitv_interv  = D.sitv_interv   $vligneType ) as QteALL
                      

                    FROM  sav_eor,sav_lor as C , sav_itv as D, agr_succ, agr_tab ser, mat_mat, agr_tab ope, outer agr_tab sec
                    WHERE seor_numor = slor_numor
                    AND seor_serv <> 'DEV'
                    AND seor_soc = 'HF'
                    AND sitv_numor = slor_numor 
                    AND sitv_interv = slor_nogrp/100
                    AND (seor_succ = asuc_num) -- OR mmat_succ = asuc_parc)
                    AND (seor_servcrt = ser.atab_code AND ser.atab_nom = 'SER')
                    AND (sitv_typitv = sec.atab_code AND sec.atab_nom = 'TYI')
                    AND (seor_ope = ope.atab_code AND ope.atab_nom = 'OPE')
                    $vStatutFacture
                 
                    AND (seor_nummat = mmat_nummat)
                   -- AND slor_constp NOT like '%ZDI%'
                    
                    $vOrvalDw
                    $vligneType

                   
                    $vConditionNoPlanning 
                    $agence
                    $vStatutInterneExterne
                    $agenceDebite
                    $serviceDebite
                    $vDateDMonthPlan
                    $vDateFMonthPlan
                    $vconditionNumParc
                    $vconditionIdMat
                    $vconditionNumOr
                    $vconditionNumSerie
                    $vconditionCasier
                    $vsection 
                    group by 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17
		                order by 10
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows;
        } finally {
            $this->databaseInformix->close();
        }
    }

    /**
     * @param PlanningSearchDto|array<string, mixed> $criteria
     */
    private function typeLigne($criteria): string
    {
        // Déterminer la valeur de typeLigne selon si criteria est un objet ou un tableau
        if (is_array($criteria)) {
            $typeLigne = $criteria['typeLigne'] ?? null;
        } elseif (is_object($criteria)) {
            $typeLigne = $criteria->typeligne;
        } else {
            throw new \InvalidArgumentException('Criteria must be an array or an object.');
        }

        // Appliquer les conditions selon typeLigne
        switch ($typeLigne) {
            case "TOUTES":
                $vtypeligne = " ";
                break;
            case "PIECES_MAGASIN":
                $constructeurPiecesMagasin = $this->parameters->get('app.constructeurs.pieces_magasin');
                $vtypeligne = " AND slor_constp in ( $constructeurPiecesMagasin ) AND slor_typlig = 'P' AND (slor_refp not like '%-L' and slor_refp not like '%-CTRL') ";
                break;
            case "ACHAT_LOCAUX":
                $constructeurAchatLocaux = $this->parameters->get('app.constructeurs.achat_locaux');
                $vtypeligne = " AND slor_constp in ( $constructeurAchatLocaux )";
                break;
            case "LUBRIFIANTS":
                $constructeurLub = $this->parameters->get('app.constructeurs.lub');
                $vtypeligne = " AND slor_constp in ( $constructeurLub )  AND slor_typlig = 'P'";
                break;
            case "PNEUMATIQUES":
                $constructeurPneumatique = $this->parameters->get('app.constructeurs.pneumatique');
                $vtypeligne = " AND slor_constp in ( $constructeurPneumatique ) ";
                break;
            default:
                $vtypeligne = " ";
                break;
        }

        return $vtypeligne;
    }

    private function planAnnee($criteria)
    {
        $yearsDatePlanifier = " CASE WHEN 
                    YEAR ( (SELECT DATE(Min(ska_d_start) ) FROM ska, skw WHERE ofh_id = seor_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id ) ) is Null 
                THEN
                    YEAR(DATE(sitv_datepla)  )
                ELSE
                    YEAR ( (SELECT DATE(Min(ska_d_start) ) FROM ska, skw WHERE ofh_id = seor_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id ) )
                END ";


        $yearsDateNonPlanifier = " YEAR ( DATE(sitv_datdeb) ) ";

        switch ($criteria->getPlan()) {
            case "PLANIFIE":
                $vYearsStatutPlan = $yearsDatePlanifier;
                break;
            case "NON_PLANIFIE":
                $vYearsStatutPlan = $yearsDateNonPlanifier;
        }
        return  $vYearsStatutPlan;
    }

    private function nonplannfierSansDatePla($criteria)
    {
        $conditionSansDatePla = " AND CASE WHEN 
                    YEAR ( (SELECT DATE(Min(ska_d_start) ) FROM ska, skw WHERE ofh_id = seor_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id ) ) is Null 
                THEN
                    YEAR(DATE(sitv_datepla)  )
                ELSE
                    YEAR ( (SELECT DATE(Min(ska_d_start) ) FROM ska, skw WHERE ofh_id = seor_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id ) )
                END is null";
        switch ($criteria->getPlan()) {
            case "PLANIFIE":
                $vNoplanniferStatutPlan = "";
                break;
            case "NON_PLANIFIE":
                $vNoplanniferStatutPlan = $conditionSansDatePla;
        }
        return  $vNoplanniferStatutPlan;
    }

    private function planMonth($criteria)
    {
        $monthDatePlanifier = " CASE WHEN 
                                    MONTH ( (SELECT DATE(Min(ska_d_start) ) FROM ska, skw WHERE ofh_id = seor_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id ) ) is Null 
                                THEN
                                    MONTH(DATE(sitv_datepla)  )
                                ELSE
                                    MONTH ( (SELECT DATE(Min(ska_d_start) ) FROM ska, skw WHERE ofh_id = seor_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id ) )
                                END  ";
        $monthDateNonPlanifier =  " MONTH ( DATE(sitv_datdeb) ) ";
        switch ($criteria->getPlan()) {
            case "PLANIFIE":
                $vMonthStatutPlan = $monthDatePlanifier;
                break;
            case "NON_PLANIFIE":
                $vMonthStatutPlan = $monthDateNonPlanifier;
        }
        return  $vMonthStatutPlan;
    }

    private function dateDebutMonthPlan($criteria)
    {

        if (!empty($criteria->getDateDebut())) {
            $monthDatePlanifier = " CASE WHEN 
                                     (SELECT DATE(Min(ska_d_start) ) FROM ska, skw WHERE ofh_id = seor_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id )  is Null 
                                THEN
                                    DATE(sitv_datepla)  
                                ELSE
                                     (SELECT DATE(Min(ska_d_start) ) FROM ska, skw WHERE ofh_id = seor_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id ) 
                                END  ";
            $monthDateNonPlanifier =  "  DATE(sitv_datdeb)  ";
            switch ($criteria->getPlan()) {
                case "PLANIFIE":
                    $vDateDMonthStatutPlan = " AND " . $monthDatePlanifier . " >= '" . $criteria->getDateDebut()->format("m/d/Y") . "'";
                    break;
                case "NON_PLANIFIE":
                    $vDateDMonthStatutPlan = " AND " . $monthDateNonPlanifier . " >= '" . $criteria->getDateDebut()->format("m/d/Y") . "'";
            }
        } else {
            $vDateDMonthStatutPlan = null;
        }
        return $vDateDMonthStatutPlan;
    }

    private function dateFinMonthPlan($criteria)
    {

        if (!empty($criteria->getDateFin())) {
            $monthDatePlanifier = " CASE WHEN 
                                    (SELECT DATE(Min(ska_d_start) ) FROM ska, skw WHERE ofh_id = seor_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id )  is Null 
                                THEN
                                    DATE(sitv_datepla)  
                                ELSE
                                     (SELECT DATE(Min(ska_d_start) ) FROM ska, skw WHERE ofh_id = seor_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id ) 
                                END  ";
            $monthDateNonPlanifier =  " DATE(sitv_datdeb)  ";
            switch ($criteria->getPlan()) {
                case "PLANIFIE":
                    $vDateFMonthStatutPlan = " AND " . $monthDatePlanifier . " <= '" . $criteria->getDateFin()->format("m/d/Y") . "'";
                    break;
                case "NON_PLANIFIE":
                    $vDateFMonthStatutPlan = " AND " . $monthDateNonPlanifier . " <= '" . $criteria->getDateFin()->format("m/d/Y") . "'";
            }
        } else {
            $vDateFMonthStatutPlan = null;
        }

        return $vDateFMonthStatutPlan;
    }

    private function facture($criteria)
    {
        switch ($criteria->getFacture()) {
            case "TOUS":
                $vStatutFacture = " AND  sitv_pos  IN ('FC','FE','CP','ST','EC')";
                break;
            case "FACTURE":
                $vStatutFacture = " AND  sitv_pos IN ('FC','FE','CP','ST')";
                break;
            case "ENCOURS":
                $vStatutFacture = " AND sitv_pos NOT IN ('FC','FE','CP','ST')";
                break;
        }

        return $vStatutFacture;
    }

    private function interneExterne($criteria)
    {
        switch ($criteria->getInterneExterne()) {
            case "TOUS":
                $vStatutInterneExterne = "";
                break;
            case "INTERNE":
                $vStatutInterneExterne = " AND SITV_NATOP = 'CES'  and SITV_TYPEOR not in ('501','601','602','603','604','605','606','607','608','609','610','611','701','702','703','704','705','706')";
                break;
            case "EXTERNE":
                $vStatutInterneExterne = "AND seor_numcli >1 ";
                break;
        }
        return $vStatutInterneExterne;
    }

    private function agence($criteria)
    {
        if (!empty($criteria->getAgence())) {
            $agence = " AND SEOR_SUCC in ('" . $criteria->getAgence() . "')";
        } else {
            $agence = "";
        }
        return $agence;
    }

    private function agenceDebite($criteria)
    {
        if (!empty($criteria->getAgenceDebite())) {
            $agenceDebite = " AND sitv_succdeb = '" . $criteria->getAgenceDebite() . "' ";
        } else {
            $agenceDebite = ""; // AND sitv_succdeb in ('01','02','90','92','40','60','50','40','30','20')
        }
        return $agenceDebite;
    }

    private function serviceDebite($criteria)
    {
        if (!empty($criteria->getServiceDebite())) {
            $serviceDebite = " AND sitv_servdeb in ('" . implode("','", $criteria->getServiceDebite()) . "')";
        } else {
            $serviceDebite = "";
        }
        return  $serviceDebite;
    }

    private function idMat($criteria)
    {
        if (!empty($criteria->getIdMat())) {
            $vconditionIdMat = " AND mmat_nummat = '" . $criteria->getIdMat() . "'";
        } else {
            $vconditionIdMat = "";
        }
        return $vconditionIdMat;
    }

    private function numOr($criteria)
    {
        if (!empty($criteria->getNumOr())) {
            $vconditionNumOr = " AND slor_numor ='" . $criteria->getNumOr() . "'";
        } else {
            $vconditionNumOr = "";
        }
        return $vconditionNumOr;
    }

    private function numSerie($criteria)
    {
        if (!empty($criteria->getNumSerie())) {
            $vconditionNumSerie = " AND TRIM(mmat_numserie) = '" . $criteria->getNumSerie() . "' ";
        } else {
            $vconditionNumSerie = "";
        }
        return $vconditionNumSerie;
    }

    private function numParc($criteria)
    {
        if (!empty($criteria->getNumParc())) {
            $vconditionNumParc = " AND mmat_recalph = '" . $criteria->getNumParc() . "'";
        } else {
            $vconditionNumParc = "";
        }
        return $vconditionNumParc;
    }

    private function casier($criteria)
    {
        if (!empty($criteria->getCasier())) {
            $vconditionCasier = " AND mmat_numparc like  '%" . $criteria->getCasier() . "%'  ";
        } else {
            $vconditionCasier = "";
        }
        return $vconditionCasier;
    }

    private function section($criteria)
    {
        if (!empty($criteria->getSection())) {
            $section = " AND sitv_typitv = '" . $criteria->getSection() . "' ";
        } else {
            $section = null;
        }
        return $section;
    }
}
