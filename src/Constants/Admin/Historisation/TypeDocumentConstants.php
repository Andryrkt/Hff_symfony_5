<?php

namespace App\Constants\Admin\Historisation;

final class TypeDocumentConstants
{
    public const TYPE_DOCUMENT_DIT_NAME = 'DIT';
    public const TYPE_DOCUMENT_OR_NAME = 'OR';
    public const TYPE_DOCUMENT_FAC_NAME = 'FAC';
    public const TYPE_DOCUMENT_RI_NAME = 'RI';
    public const TYPE_DOCUMENT_TIK_NAME = 'TIK';
    public const TYPE_DOCUMENT_DA_NAME = 'DA';
    public const TYPE_DOCUMENT_DOM_NAME = 'DOM';
    public const TYPE_DOCUMENT_BADM_NAME = 'BADM';
    public const TYPE_DOCUMENT_CAS_NAME = 'CAS';
    public const TYPE_DOCUMENT_CDE_NAME = 'CDE';
    public const TYPE_DOCUMENT_DEV_NAME = 'DEV';
    public const TYPE_DOCUMENT_BC_NAME = 'BC';
    public const TYPE_DOCUMENT_AC_NAME = 'AC';
    public const TYPE_DOCUMENT_SW_NAME = 'SW';
    public const TYPE_DOCUMENT_MUT_NAME = 'MUT';

    public static function getAllTypes(): array
    {
        return [
            self::TYPE_DOCUMENT_DIT_NAME,
            self::TYPE_DOCUMENT_OR_NAME,
            self::TYPE_DOCUMENT_FAC_NAME,
            // ... tous les types
        ];
    }

    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_DOCUMENT_DIT_NAME => 'Document DIT',
            self::TYPE_DOCUMENT_OR_NAME => 'Ordre de mission',
            // ... labels pour chaque type
        ];
    }
}
