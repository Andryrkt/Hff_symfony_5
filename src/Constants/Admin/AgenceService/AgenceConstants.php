<?php

namespace App\Constants\Admin\AgenceService;

class AgenceConstants
{
    public const CODE_AGENCE_ANTANARIVO = '01';
    public const CODE_AGENCE_CESSNA_IVATO = '02';
    public const CODE_AGENCE_FORT_DAUPHIN = '20';
    public const CODE_AGENCE_AMBATOVY = '30';
    public const CODE_AGENCE_TAMATAVE = '40';
    public const CODE_AGENCE_RENTAL = '50';
    public const CODE_AGENCE_PNEU_OUTIL_LUB = '60';
    public const CODE_AGENCE_ADMINISTRATION = '80';
    public const CODE_AGENCE_COMM_ENERGIE = '90';
    public const CODE_AGENCE_ENERGIE_DURABLE = '91';
    public const CODE_AGENCE_ENERGIE_JIRAMA = '92';
    public const CODE_AGENCE_TRAVEL_AIRWAYS = 'C1';


    public const NOM_AGENCE_ANTANARIVO = 'ANTANANARIVO';
    public const NOM_AGENCE_CESSNA_IVATO = 'CESSNA IVATO';
    public const NOM_AGENCE_FORT_DAUPHIN = 'FORT-DAUPHIN';
    public const NOM_AGENCE_AMBATOVY = 'AMBATOVY';
    public const NOM_AGENCE_TAMATAVE = 'TAMATAVE';
    public const NOM_AGENCE_RENTAL = 'RENTAL';
    public const NOM_AGENCE_PNEU_OUTIL_LUB = 'PNEU - OUTIL - LUB';
    public const NOM_AGENCE_ADMINISTRATION = 'ADMINISTRATION';
    public const NOM_AGENCE_COMM_ENERGIE = 'COMM ENERGIE';
    public const NOM_AGENCE_ENERGIE_DURABLE = 'ENERGIE DURABLE';
    public const NOM_AGENCE_ENERGIE_JIRAMA = 'ENERGIE JIRAMA';
    public const NOM_AGENCE_TRAVEL_AIRWAYS = 'TRAVEL AIRWAYS';

    public const CODE_AGENCE_ENERGIE = [
        self::CODE_AGENCE_COMM_ENERGIE,
        self::CODE_AGENCE_ENERGIE_DURABLE,
        self::CODE_AGENCE_ENERGIE_JIRAMA
    ];
}
