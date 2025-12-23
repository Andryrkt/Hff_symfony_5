/**
 * Utilitaire pour la validation générale des formulaires
 */

export interface ValidationResult {
    isValid: boolean;
    errors: string[];
}

/**
 * Valide tous les champs obligatoires d'un formulaire
 * @param form Le formulaire à valider
 * @returns Résultat de la validation avec la liste des erreurs
 */
export function validateFormFields(form: HTMLFormElement): ValidationResult {
    let isValid = true;
    const errors: string[] = [];
    const requiredFields = form.querySelectorAll<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>('[required]');

    requiredFields.forEach((field) => {
        const errorElement = document.querySelector(`#error-${field.id}`);
        const fieldName = field.dataset.fieldName || field.name || field.id || 'Ce champ';

        // Vérifier si le champ est vide
        const isEmpty = !field.value.trim();

        if (isEmpty) {
            // Ajouter une bordure rouge
            field.classList.add('border', 'border-danger');

            // Créer le message d'erreur
            const errorMessage = `Le champ "<span class="text-danger text-decoration-underline">${fieldName}</span>" est obligatoire`;
            errors.push(errorMessage);

            // Afficher l'erreur si un élément d'erreur existe
            if (errorElement) {
                errorElement.textContent = errorMessage;
                errorElement.classList.add('text-danger');
            }

            isValid = false;
        } else {
            // Retirer la bordure rouge si le champ est valide
            field.classList.remove('border', 'border-danger');

            // Effacer le message d'erreur
            if (errorElement) {
                errorElement.textContent = '';
                errorElement.classList.remove('text-danger');
            }
        }
    });

    return { isValid, errors };
}

/**
 * Réinitialise les erreurs de validation d'un formulaire
 * @param form Le formulaire à réinitialiser
 */
export function resetFormValidation(form: HTMLFormElement): void {
    const fields = form.querySelectorAll<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>('.border-danger');

    fields.forEach((field) => {
        field.classList.remove('border', 'border-danger');

        const errorElement = document.querySelector(`#error-${field.id}`);
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.classList.remove('text-danger');
        }
    });
}
