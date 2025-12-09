/**
 * Constantes pour les formulaires RH
 */

export const FORM_CONSTANTS = {
    // Validation
    MOBILE_MONEY_MAX_DIGITS: 10,

    // API
    API_TIMEOUT: 5000,
    API_ENDPOINTS: {
        MISSION_OVERLAP: '/api/validation/mission-overlap',
        CODE_BANCAIRE: '/api/rh/dom/mode',
        INDEMNITE_FORFAITAIRE: '/api/rh/dom/indemnite-forfaitaire',
    },

    // Performance
    DEBOUNCE_DELAY: 500,
    FOCUS_DELAY: 100,

    // Business rules
    MAX_AMOUNT_WARNING: 500000,

    // Longueurs max des champs
    FIELD_MAX_LENGTHS: {
        motifDeplacement: 60,
        client: 30,
        lieuIntervention: 60,
        motifAutresDepense1: 30,
        motifAutresDepense2: 30,
        motifAutresDepense3: 30,
    },

    // Types de mission
    MISSION_TYPES: {
        MISSION: 'MISSION',
        TROP_PERCU: 'TROP PERCU',
        FRAIS_EXCEPTIONNEL: 'FRAIS EXCEPTIONNEL',
    },

    // Modes de paiement
    PAYMENT_MODES: {
        ESPECE: 'ESPECE',
        VIREMENT: 'VIREMENT BANCAIRE',
        MOBILE_MONEY: 'MOBILE MONEY',
    },

    // Types de salarié
    EMPLOYEE_TYPES: {
        PERMANENT: 'PERMANENT',
        TEMPORAIRE: 'TEMPORAIRE',
    },
};

export default FORM_CONSTANTS;
