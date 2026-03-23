<?php

namespace App\Constants\Hf\Atelier\Dit\Soumission\Ors;

class StatutOrConstant
{
    public const SOUMIS_A_VALIDATION = 'Soumis à validation';
    public const VALIDE = 'Validé';
    public const REFUSE = 'Refusé';
    public const MODIFICATION_DEMANDE_PAR_CLIENT = 'Modification demandée par client';
    public const MODIFICATION_DEMANDE_PAR_CA = 'Modification demandée par CA';
    public const MODIFICATION_DEMANDE_PAR_DT = 'Modification demandée par DT';

    public const TOUT_LIVRE = 'Tout livré';
    public const PARTIELLEMENT_LIVRE = 'Partiellement livré';
    public const PARTIELLEMENT_DISPO = 'Partiellement dispo';
    public const COMPLET_NON_LIVRE = 'Complet non livré';


    public const CSS_CLASSES = [
        self::TOUT_LIVRE => 'bg-success text-white',
        self::PARTIELLEMENT_LIVRE => 'bg-warning text-white',
        self::PARTIELLEMENT_DISPO => 'bg-info text-white',
        self::COMPLET_NON_LIVRE => 'bg-primary text-white',
    ];

    /**
     * Retourne la classe CSS appropriée pour le statut de la commande
     * 
     * @param string $statut
     * @return string
     */
    public static function getCssClass(string $statut): string
    {
        return self::CSS_CLASSES[$statut] ?? '';
    }
}
