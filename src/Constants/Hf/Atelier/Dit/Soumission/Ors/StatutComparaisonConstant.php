<?php

namespace App\Constants\Hf\Atelier\Dit\Soumission\Ors;

/**
 * Constantes pour les résultats de comparaison de données (Avant/Après)
 */
class StatutComparaisonConstant
{
    public const NOUVEAU = 'Nouv';
    public const SUPPRIME = 'Supp';
    public const MODIFIE = 'Modif';
    public const IDENTIQUE = 'Identique';

    /**
     * Styles CSS/HTML pour l'affichage dans les tableaux PDF
     */
    public const PDF_STYLES = [
        self::NOUVEAU  => 'background-color: #00FF00;', // Vert
        self::SUPPRIME => 'background-color: #FF0000;', // Rouge
        self::MODIFIE  => 'background-color: #FFFF00;', // Jaune
        self::IDENTIQUE => '',
    ];

    /**
     * Retourne le style PDF pour un statut donné
     */
    public static function getPdfStyle(?string $statut): string
    {
        return self::PDF_STYLES[$statut] ?? '';
    }
}
