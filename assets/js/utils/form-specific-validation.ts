/**
 * Validations spécifiques par formulaire
 */

export interface SpecificValidationResult {
    isValid: boolean;
    title?: string;
    message: string;
}

/**
 * Valide un formulaire avec des règles spécifiques
 * @param form Le formulaire à valider
 * @param formId L'identifiant du formulaire
 * @returns Résultat de la validation spécifique
 */
export async function validateSpecificForm(
    form: HTMLFormElement,
    formId: string
): Promise<SpecificValidationResult> {

    // Récupérer l'ID du formulaire ou un attribut data-form-id
    const actualFormId = formId || form.id || form.dataset.formId;

    switch (actualFormId) {
        case '#casier-first-form':
        case 'casier-first-form':
            return validateCasierFirstForm(form);

        case '#casier-second-form':
        case 'casier-second-form':
            return validateCasierSecondForm(form);

        case '#dom-second-form':
        case 'dom-second-form':
            return validateDomSecondForm(form);

        default:
            // Pas de validation spécifique pour ce formulaire
            return {
                isValid: true,
                message: ''
            };
    }
}

/**
 * Validation spécifique pour le premier formulaire de casier
 * Au moins un des trois champs (idMateriel, numParc, numSerie) doit être rempli
 */
function validateCasierFirstForm(form: HTMLFormElement): SpecificValidationResult {
    const idMateriel = form.querySelector<HTMLInputElement>('[name*="idMateriel"]');
    const numParc = form.querySelector<HTMLInputElement>('[name*="numParc"]');
    const numSerie = form.querySelector<HTMLInputElement>('[name*="numSerie"]');

    const hasIdMateriel = idMateriel?.value.trim() !== '';
    const hasNumParc = numParc?.value.trim() !== '';
    const hasNumSerie = numSerie?.value.trim() !== '';

    if (!hasIdMateriel && !hasNumParc && !hasNumSerie) {
        return {
            isValid: false,
            title: 'Champs manquants',
            message: 'Au moins un des trois champs (<strong>Id Materiel</strong>, <strong>N° Parc</strong>, <strong>N° Serie</strong>) doit être rempli.'
        };
    }

    return {
        isValid: true,
        message: ''
    };
}

/**
 * Validation spécifique pour le second formulaire de casier
 */
function validateCasierSecondForm(form: HTMLFormElement): SpecificValidationResult {
    // Ajouter des validations spécifiques si nécessaire
    return {
        isValid: true,
        message: ''
    };
}

/**
 * Validation spécifique pour le formulaire DOM
 */
function validateDomSecondForm(form: HTMLFormElement): SpecificValidationResult {
    // Ajouter des validations spécifiques si nécessaire
    return {
        isValid: true,
        message: ''
    };
}
