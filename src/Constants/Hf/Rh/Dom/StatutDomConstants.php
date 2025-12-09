<?php

namespace App\Constants\Hf\Rh\Dom;

/**
 * Constantes pour les statuts des ordres de mission (DOM)
 * et leurs classes CSS associées
 */
final class StatutDomConstants
{
    // Statuts
    public const STATUT_OUVERT = 'OUVERT';
    public const STATUT_PAYE = 'PAYE';
    public const STATUT_ATTENTE_PAIEMENT = 'ATTENTE PAIEMENT';
    public const STATUT_CONTROLE_SERVICE = 'CONTROLE SERVICE';
    public const STATUT_A_VALIDER_SERVICE_EMETTEUR = 'A VALIDER SERVICE EMETTEUR';
    public const STATUT_VALIDE = 'VALIDE';
    public const STATUT_VALIDE_COMPTABILITE = 'VALIDE COMPTABILITE';
    public const STATUT_VALIDATION_RH = 'VALIDATION RH';
    public const STATUT_VALIDATION_DG = 'VALIDATION DG';
    public const STATUT_ANNULE_CHEF_SERVICE = 'ANNULE CHEF DE SERVICE';
    public const STATUT_ANNULE_COMPTABILITE = 'ANNULE COMPTABILITE';
    public const STATUT_ANNULE_SECRETARIAT_RH = 'ANNULE SECRETARIAT RH';
    public const STATUT_ANNULE = 'ANNULE';

    // Classes CSS par statut
    public const CSS_CLASS_MAP = [
        self::STATUT_OUVERT => 'bg-warning bg-gradient text-center',
        self::STATUT_PAYE => 'bg-success bg-gradient',
        self::STATUT_ATTENTE_PAIEMENT => 'bg-success',
        self::STATUT_CONTROLE_SERVICE => 'bg-info',
        self::STATUT_A_VALIDER_SERVICE_EMETTEUR => 'bg-primary',
        self::STATUT_VALIDE => 'bg-success',
        self::STATUT_VALIDE_COMPTABILITE => 'bg-success',
        self::STATUT_VALIDATION_RH => 'bg-success',
        self::STATUT_VALIDATION_DG => 'bg-success',
        self::STATUT_ANNULE_CHEF_SERVICE => 'bg-danger',
        self::STATUT_ANNULE_COMPTABILITE => 'bg-danger',
        self::STATUT_ANNULE_SECRETARIAT_RH => 'bg-danger',
        self::STATUT_ANNULE => 'bg-danger',
    ];

    // Statuts nécessitant une opacité réduite
    public const STATUTS_WITH_OPACITY = [
        self::STATUT_ATTENTE_PAIEMENT,
        self::STATUT_A_VALIDER_SERVICE_EMETTEUR,
    ];

    /**
     * Retourne la classe CSS pour un statut donné
     */
    public static function getCssClass(string $statut): string
    {
        return self::CSS_CLASS_MAP[$statut] ?? '';
    }

    /**
     * Retourne le style CSS inline pour un statut donné
     */
    public static function getCssStyle(string $statut): string
    {
        return in_array($statut, self::STATUTS_WITH_OPACITY)
            ? '--bs-bg-opacity: .5;'
            : '';
    }
}
