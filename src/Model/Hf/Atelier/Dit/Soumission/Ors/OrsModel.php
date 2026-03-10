<?php

namespace App\Model\Hf\Atelier\Dit\Soumission\Ors;

use App\Model\DatabaseInformix;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OrsModel
{

    private DatabaseInformix $databaseInformix;
    private ParameterBagInterface $parameters;

    public function __construct(
        DatabaseInformix $databaseInformix,
        ParameterBagInterface $parameters
    ) {
        $this->databaseInformix = $databaseInformix;
        $this->parameters = $parameters;
    }


    public function getInfoOrs(string $numeroDit, int $numeroOr): array
    {
        $this->databaseInformix->connect();

        try {
            $statement = "SELECT
                sitv_numor as numero_or,
                sitv_datdeb,
                trim(seor_refdem) as numero_dit,
                sitv_interv as numero_itv,
                trim(sitv_comment) as libelle_itv,
                seor_numdev as numero_devis,
                slor_constp as constructeur,
                trim(slor_refp) as reference,
                trim(slor_desi) as designation,
                slor_typlig as type_ligne,
                nvl (slor_qterel, 0) + nvl (slor_qterea, 0) + nvl (slor_qteres, 0) + nvl (slor_qtewait, 0) - nvl (slor_qrec, 0) as qte_piece,
                slor_qterea as qte_autre,
                slor_pxnreel as prix_net

                FROM informix.sav_eor
                INNER JOIN informix.sav_itv ON seor_numor = sitv_numor
                LEFT JOIN informix.sav_lor ON sitv_numor = slor_numor AND sitv_interv = slor_nogrp / 100
                WHERE
                    seor_serv <> 'DEV'
                    AND trim(seor_refdem) = '$numeroDit'
                    AND sitv_numor = $numeroOr
                ORDER BY sitv_numor, sitv_interv
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows;
        } finally {
            $this->databaseInformix->close();
        }
    }

    public function getInfoOrsPourConstructeurMagasin(string $numeroDit, int $numeroOr): array
    {
        $this->databaseInformix->connect();

        try {
            $piecesMagasin = $this->parameters->get('app.constructeurs.pieces_magasin');

            $statement = " SELECT
                slor_numor as numero_or,
                sitv_datdeb,
                trim(seor_refdem) as numero_dit,
                sitv_interv as numero_itv,
                trim(sitv_comment) as libelle_itv,
                slor_constp as constructeur,
                trim(slor_refp) as reference,
                trim(slor_desi) as designation,
                slor_succ as code_agence,
                slor_servcrt as code_service
               

                FROM informix.sav_eor, informix.sav_lor, informix.sav_itv
                WHERE
                    seor_numor = slor_numor
                    AND seor_serv <> 'DEV'
                    AND sitv_numor = slor_numor
                    AND sitv_interv = slor_nogrp / 100

                AND trim(seor_refdem) = '$numeroDit'
                AND slor_numor = $numeroOr 
                AND slor_constp in ($piecesMagasin)
                ORDER BY slor_numor, sitv_interv
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows;
        } finally {
            $this->databaseInformix->close();
        }
    }

    public function getPieceFaibleActiviteAchat(string $constructeur, ?string $reference, string $numOr): array
    {

        $this->databaseInformix->connect();

        try {
            $piecesMagasin = $this->parameters->get('app.constructeurs.pieces_magasin');

            $statement = "SELECT
                TRIM(case when 
                    A.nombre_jour >= 365 then 'a afficher'
                    else 'ne pas afficher'
                end) as retour
                , A.ffac_datef as date_derniere_cde
                , (select distinct slor_pmp from sav_lor where slor_numor = '$numOr' and slor_constp = '$constructeur' and slor_refp = '$reference') as pmp
                FROM
                (select first 1  
                ffac_datef
                , TODAY - ffac_datef as nombre_jour
                , fllf_numfac,*
                from informix.frn_llf 
                inner join informix.frn_fac on ffac_soc = fllf_soc and ffac_succ = fllf_succ and ffac_numfac = fllf_numfac
                inner join informix.frn_cde on fcde_soc = fllf_soc and fcde_succ = fllf_succ and fcde_numcde = fllf_numcde
                --inner join art_hpm on ahpm_soc = fllf_soc and ahpm_succfac = fllf_succ and ahpm_numfac = fllf_numfac and ahpm_constp = fllf_constp and ahpm_refp = fllf_refp
                where fllf_constp = '$constructeur'
                and fllf_refp = '$reference'
                and fllf_succ = '01'
                and ffac_serv = 'NEG'
                and fllf_soc = 'HF'
                and fcde_numfou not in (select asuc_num from informix.agr_succ where asuc_numsoc = 'HF')
                and fllf_qtefac > 0
                and fllf_constp in ($piecesMagasin)
                order by ffac_numfac desc) as A
        ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows;
        } finally {
            $this->databaseInformix->close();
        }
    }

    public function getNumeroDevis(int $numeroOr): ?int
    {
        $this->databaseInformix->connect();

        try {
            $statement = "SELECT  seor_numdev  as numero_devis
                from sav_eor
                where seor_numor = $numeroOr
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return (int)$rows[0]['numero_devis'] ?? null;
        } finally {
            $this->databaseInformix->close();
        }
    }


    public function getDatePlanning(int $numeroOr, int $numeroItv): ?\DateTime
    {
        $this->databaseInformix->connect();

        try {
            $statement = "SELECT 
                    CASE 
                            WHEN 
                                (SELECT DATE(Min(ska_d_start)) FROM informix.ska, informix.skw WHERE ofh_id = sitv_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id )  is Null 
                            THEN DATE(sitv_datepla)  
                            ELSE
                                (SELECT DATE(Min(ska_d_start)) FROM informix.ska, informix.skw WHERE ofh_id = sitv_numor AND ofs_id=sitv_interv AND skw.skw_id = ska.skw_id ) 
                    END as date_planning
                    FROM informix.sav_itv 
                where sitv_numor = $numeroOr
                and sitv_interv = $numeroItv
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            if (isset($rows[0]['date_planning']) && !empty($rows[0]['date_planning'])) {
                return new \DateTime($rows[0]['date_planning']);
            }
            return null;
        } finally {
            $this->databaseInformix->close();
        }
    }

    // public function getPieceSortieMagasin(int $numeroOr): string
    // {
    //     $this->databaseInformix->connect();

    //     try {
    //         $piecesMagasin = $this->parameters->get('app.constructeurs.pieces_magasin');

    //         $statement = " SELECT
    //             CASE WHEN count(slor_constp) > 0 THEN 'OUI' ELSE 'NON' END as nbr_sortie_magasin 
    //             from sav_lor 
    //             where slor_constp in ($piecesMagasin)
    //             AND (slor_refp not like '%-L' and slor_refp not like '%-CTRL')
    //             and slor_typlig = 'P' 
    //             and slor_numor = '$numeroOr'
    //         ";

    //         $result = $this->databaseInformix->executeQuery($statement);
    //         $rows = $this->databaseInformix->fetchResults($result);

    //         return $rows[0]['nbr_sortie_magasin'] ?? 'NON';
    //     } finally {
    //         $this->databaseInformix->close();
    //     }
    // }

    public function getSuffixSelonConstructeurPieceMagasin(int $numeroOr): string
    {
        $this->databaseInformix->connect();

        try {
            $piecesMagasinSansCat = $this->parameters->get('app.constructeurs.pieces_magasin_sans_cat');

            $statement = " SELECT
            CASE
                -- si CAT et autre constructeur magasin
                WHEN COUNT(CASE WHEN slor_constp = 'CAT' THEN 1 END) > 0
                AND COUNT(CASE WHEN slor_constp IN ($piecesMagasinSansCat) THEN 1 END) > 0
                THEN TRIM('CP')
                -- si  CAT
                WHEN COUNT(CASE WHEN slor_constp = 'CAT' THEN 1 END) > 0
                AND COUNT(CASE WHEN slor_constp IN ($piecesMagasinSansCat) THEN 1 END) = 0
                THEN TRIM('C')
                -- si ni CAT ni autre constructeur magasin
                WHEN COUNT(CASE WHEN slor_constp = 'CAT' THEN 1 END) = 0
                AND COUNT(CASE WHEN slor_constp IN ($piecesMagasinSansCat) THEN 1 END) = 0
                THEN TRIM('N')
                -- si autre constructeur magasin
                WHEN COUNT(CASE WHEN slor_constp = 'CAT' THEN 1 END) = 0
                AND COUNT(CASE WHEN slor_constp IN ($piecesMagasinSansCat) THEN 1 END) > 0
                THEN TRIM('P')
                -- sinon
                ELSE ''
            END AS retour
        FROM sav_lor
        WHERE slor_numor = '" . $numeroOr . "'
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows[0]['retour'] ?? '';
        } finally {
            $this->databaseInformix->close();
        }
    }
}
