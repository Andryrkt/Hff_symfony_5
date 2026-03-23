<?php

namespace App\Constants\Admin\Historisation;

final class TypeDocumentConstants
{
    public const TYPE_DOCUMENT_DIT_CODE = 'DIT';
    public const TYPE_DOCUMENT_OR_CODE = 'OR';
    public const TYPE_DOCUMENT_FAC_CODE = 'FAC';
    public const TYPE_DOCUMENT_RI_CODE = 'RI';
    public const TYPE_DOCUMENT_TIK_CODE = 'TIK';
    public const TYPE_DOCUMENT_DA_CODE = 'DA';
    public const TYPE_DOCUMENT_DOM_CODE = 'DOM';
    public const TYPE_DOCUMENT_BADM_CODE = 'BDM';
    public const TYPE_DOCUMENT_CAS_CODE = 'CAS';
    public const TYPE_DOCUMENT_CDE_CODE = 'CDE';
    public const TYPE_DOCUMENT_DEV_CODE = 'DEV';
    public const TYPE_DOCUMENT_BC_CODE = 'BC';
    public const TYPE_DOCUMENT_AC_CODE = 'AC';
    public const TYPE_DOCUMENT_SW_CODE = 'SW';
    public const TYPE_DOCUMENT_MUT_CODE = 'MUT';

    public static function getAllTypes(): array
    {
        return [
            self::TYPE_DOCUMENT_DIT_CODE,
            self::TYPE_DOCUMENT_OR_CODE,
            self::TYPE_DOCUMENT_FAC_CODE,
            // ... tous les types
        ];
    }

    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_DOCUMENT_DIT_CODE => 'Document DIT',
            self::TYPE_DOCUMENT_OR_CODE => 'Ordre de mission',
            // ... labels pour chaque type
        ];
    }
}
