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
            $statement = " SELECT
                slor_numor as numero_or,
                sitv_datdeb,
                trim(seor_refdem) as numero_dit,
                sitv_interv as numero_itv,
                trim(sitv_comment) as libelle_itv,
                count(slor_constp) as nombre_ligne,
                slor_constp as constructeur,
                trim(slor_refp) as reference,
                trim(slor_desi) as designation,
                slor_succ as code_agence,
                slor_servcrt as code_service,
                -- montant intervention
                Sum(
                    CASE
                        WHEN slor_typlig = 'P' THEN (
                            slor_qterel + slor_qterea + slor_qteres + slor_qtewait - slor_qrec
                        )
                        WHEN slor_typlig IN ('F', 'M', 'U', 'C') THEN slor_qterea
                    END * CASE
                        WHEN slor_typlig = 'P' THEN slor_pxnreel
                        WHEN slor_typlig IN ('F', 'M', 'U', 'C') THEN slor_pxnreel
                    END
                ) as montant_itv,
                -- montant pièce
                Sum(
                    CASE
                        WHEN slor_typlig = 'P'
                        AND slor_constp NOT like 'Z%'
                        AND slor_constp <> 'LUB' THEN (
                            nvl (slor_qterel, 0) + nvl (slor_qterea, 0) + nvl (slor_qteres, 0) + nvl (slor_qtewait, 0) - nvl (slor_qrec, 0)
                        )
                    END * CASE
                        WHEN slor_typlig = 'P' THEN slor_pxnreel
                        WHEN slor_typlig IN ('F', 'M', 'U', 'C') THEN slor_pxnreel
                    END
                ) AS montant_piece,
                -- montant main d'oeuvre
                Sum(
                    CASE
                        WHEN slor_typlig = 'M' THEN slor_qterea
                    END * CASE
                        WHEN slor_typlig = 'P' THEN slor_pxnreel
                        WHEN slor_typlig IN ('F', 'M', 'U', 'C') THEN slor_pxnreel
                    END
                ) AS montant_mo,
                -- montant achats locaux
                Sum(
                    CASE
                        WHEN slor_constp = 'ZST' THEN (
                            slor_qterel + slor_qterea + slor_qteres + slor_qtewait - slor_qrec
                        )
                    END * CASE
                        WHEN slor_typlig = 'P' THEN slor_pxnreel
                        WHEN slor_typlig IN ('F', 'M', 'U', 'C') THEN slor_pxnreel
                    END
                ) AS montant_achats_locaux,
                -- montant divers
                Sum(
                    CASE
                        WHEN slor_constp <> 'ZST'
                        AND slor_constp like 'Z%' THEN slor_qterea
                    END * CASE
                        WHEN slor_typlig = 'P' THEN slor_pxnreel
                        WHEN slor_typlig IN ('F', 'M', 'U', 'C') THEN slor_pxnreel
                    END
                ) AS montant_divers,
                -- montant lubrifiants
                Sum(
                    CASE
                        WHEN slor_typlig = 'P'
                        AND slor_constp NOT like 'Z%'
                        AND slor_constp = 'LUB' THEN (
                            nvl (slor_qterel, 0) + nvl (slor_qterea, 0) + nvl (slor_qteres, 0) + nvl (slor_qtewait, 0) - nvl (slor_qrec, 0)
                        )
                    END * CASE
                        WHEN slor_typlig = 'P' THEN slor_pxnreel
                        WHEN slor_typlig IN ('F', 'M', 'U', 'C') THEN slor_pxnreel
                    END
                ) AS montant_lubrifiants

                FROM informix.sav_eor, informix.sav_lor, informix.sav_itv
                WHERE
                    seor_numor = slor_numor
                    AND seor_serv <> 'DEV'
                    AND sitv_numor = slor_numor
                    AND sitv_interv = slor_nogrp / 100

                AND trim(seor_refdem) = '$numeroDit'
                AND slor_numor = $numeroOr
                GROUP BY 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
                ORDER BY slor_numor, sitv_interv
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

    public function getPieceSortieMagasin(int $numeroOr): string
    {
        $this->databaseInformix->connect();

        try {
            $piecesMagasin = $this->parameters->get('app.constructeurs.pieces_magasin');

            $statement = " SELECT
                CASE WHEN count(slor_constp) > 0 THEN 'OUI' ELSE 'NON' END as nbr_sortie_magasin 
                from sav_lor 
                where slor_constp in ($piecesMagasin)
                AND (slor_refp not like '%-L' and slor_refp not like '%-CTRL')
                and slor_typlig = 'P' 
                and slor_numor = '$numeroOr'
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows[0]['nbr_sortie_magasin'] ?? 'NON';
        } finally {
            $this->databaseInformix->close();
        }
    }

    public function getPieceAchatLocaux(int $numeroOr): string
    {
        $this->databaseInformix->connect();

        try {
            $piecesAchatLocaux = $this->parameters->get('app.constructeurs.achat_locaux');

            $statement = " SELECT
                CASE WHEN count(slor_constp) > 0 THEN 'OUI' ELSE 'NON' END as nbr_achat_locaux 
            from sav_lor 
            where slor_constp in ($piecesAchatLocaux)
            and slor_numor = '$numeroOr'
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows[0]['nbr_achat_locaux'] ?? 'NON';
        } finally {
            $this->databaseInformix->close();
        }
    }

    public function getPiecePol(int $numeroOr): string
    {
        $this->databaseInformix->connect();

        try {
            $piecesPol = $this->parameters->get('app.constructeurs.lub');

            $statement = " SELECT
                CASE WHEN count(slor_constp) > 0 THEN 'OUI' ELSE 'NON' END as nbr_pol 
            from sav_lor 
            where slor_constp in ($piecesPol)
            and slor_numor = '$numeroOr'
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows[0]['nbr_pol'] ?? 'NON';
        } finally {
            $this->databaseInformix->close();
        }
    }
}
