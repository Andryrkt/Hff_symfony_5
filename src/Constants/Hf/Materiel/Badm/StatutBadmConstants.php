<?php

namespace App\Constants\Hf\Materiel\Badm;

final class StatutBadmConstants
{
    public const STATUT_ENCOURS = 'ENCOURS';
    public const STATUT_OUVERT = 'OUVERT';
    public const STATUT_CLOTURE = 'CLOTURE';
    public const STATUT_ANNULE = 'ANNULE';
    public const STATUT_A_VALIDER_SERVICE_EMETTEUR = 'A VALIDER SERVICE EMETTEUR';
    public const STATUT_ANNULE_SERVICE_EMETTEUR = 'ANNULE SERVICE EMETTEUR';
    public const STATUT_A_VALIDER_SERVICE_DESTINATAIRE = 'A VALIDER SERVICE DESTINATAIRE';
    public const STATUT_ANNULE_SERVICE_DESTINATAIRE = 'ANNULE SERVICE DESTINATAIRE';
    public const STATUT_ATTENTE_VALIDATION_DG = 'ATTENTE VALIDATION DG';
    public const STATUT_ANNULE_DG = 'ANNULE DG';
    public const STATUT_A_TRAITER_INFO = 'A TRAITER INFO';
    public const STATUT_A_TRAITER_COMPTA = 'A TRAITER COMPTA';
    public const STATUT_CLOTURE_COMPTA = 'CLOTURE COMPTA';
    public const STATUT_ANNULE_INFORMATIQUE = 'ANNULE INFORMATIQUE';

    public static function getStatutsEncoursDeTraitement()
    {
        return [
            self::STATUT_OUVERT,
            self::STATUT_ENCOURS,
            self::STATUT_A_VALIDER_SERVICE_EMETTEUR,
            self::STATUT_A_VALIDER_SERVICE_DESTINATAIRE,
            self::STATUT_ATTENTE_VALIDATION_DG,
            self::STATUT_A_TRAITER_INFO,
            self::STATUT_A_TRAITER_COMPTA
        ];
    }

    //Classes Css par statut
    public const CSS_CLASS_MAP = [
        self::STATUT_OUVERT => 'bg-warning bg-gradient text-cente',
        self::STATUT_CLOTURE => 'bg-success bg-gradient',
        self::STATUT_CLOTURE_COMPTA => 'bg-success bg-gradient',
        self::STATUT_A_VALIDER_SERVICE_DESTINATAIRE => 'bg-info',
        self::STATUT_ANNULE_INFORMATIQUE => 'bg-danger',
        self::STATUT_ANNULE_SERVICE_DESTINATAIRE => 'bg-danger',
        self::STATUT_ANNULE_SERVICE_EMETTEUR => 'bg-danger',
        self::STATUT_ANNULE => 'bg-danger',
        self::STATUT_ATTENTE_VALIDATION_DG => 'bg-primary',
        self::STATUT_A_VALIDER_SERVICE_EMETTEUR => 'bg-primary'


    ];

    // Statut nécessitant une opacité réduite
    public const STATUTS_WITH_OPACITY = [
        self::STATUT_A_VALIDER_SERVICE_EMETTEUR
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
