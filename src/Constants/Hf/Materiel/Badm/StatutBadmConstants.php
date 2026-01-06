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
}
