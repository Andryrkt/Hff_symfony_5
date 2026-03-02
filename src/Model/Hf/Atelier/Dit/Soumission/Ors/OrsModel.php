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


    public function getInfoOrs(string $numeroDit): array
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
                GROUP BY 1, 2, 3, 4, 5
                ORDER BY slor_numor, sitv_interv
            ";

            $result = $this->databaseInformix->executeQuery($statement);
            $rows = $this->databaseInformix->fetchResults($result);

            return $rows;
        } finally {
            $this->databaseInformix->close();
        }
    }
}
