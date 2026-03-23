<?php

namespace App\Constants\Hf\Atelier\Dit;

final class StatutDitConstants
{
    public const STATUT_A_AFFECTER = 'A AFFECTER';
    public const STATUT_AFFECTEE_SECTION = 'AFFECTEE SECTION';
    public const STATUT_CLOTUREE_VALIDEE = 'CLOTUREE VALIDEE';
    public const STATUT_CLOTUREE_ANNULEE = 'CLOTUREE ANNULEE';
    public const STATUT_CLOTUREE_HORS_DELAI = 'CLOTUREE HORS DELAI';
    public const STATUT_TERMINEE = 'TERMINEE';

    public const CSS_CLASS_MAP = [
        self::STATUT_A_AFFECTER => 'a_affecter',
        self::STATUT_AFFECTEE_SECTION => 'affectee_section',
        self::STATUT_CLOTUREE_VALIDEE => 'cloturee_validee',
        self::STATUT_CLOTUREE_ANNULEE => 'cloturee_annulee',
        self::STATUT_CLOTUREE_HORS_DELAI => 'cloturee_hors_delai',
        self::STATUT_TERMINEE => 'terminee',
    ];

    /**
     * Retourne la classe CSS pour un statut donné
     */
    public static function getCssClass(string $statut): string
    {
        return self::CSS_CLASS_MAP[$statut] ?? '';
    }
}
