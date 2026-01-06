<?php

namespace App\Constants\Hf\Materiel\Badm;

final class TypeMouvementConstants
{
    public const TYPE_MOUVEMENT_ENTREE_EN_PARC = 'ENTREE EN PARC';
    public const TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE = 'CHANGEMENT AGENCE/SERVICE';
    public const TYPE_MOUVEMENT_CHANGEMENT_DE_CASIER = 'CHANGEMENT DE CASIER';
    public const TYPE_MOUVEMENT_CESSION_DACTIF = 'CESSION D\'ACTIF';
    public const TYPE_MOUVEMENT_MISE_AU_REBUT = 'MISE AU REBUT';

    public const CSS_CLASS_MAP = [
        self::TYPE_MOUVEMENT_ENTREE_EN_PARC => 'bg-success',
        self::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE => 'bg-info',
        self::TYPE_MOUVEMENT_CHANGEMENT_DE_CASIER => 'bg-secondary',
        self::TYPE_MOUVEMENT_CESSION_DACTIF => 'bg-danger',
        self::TYPE_MOUVEMENT_MISE_AU_REBUT => 'bg-warning',
    ];

    /**
     * Retourne la classe CSS pour un statut donné
     */
    public static function getCssClass(string $statut): string
    {
        return self::CSS_CLASS_MAP[$statut] ?? '';
    }
}
