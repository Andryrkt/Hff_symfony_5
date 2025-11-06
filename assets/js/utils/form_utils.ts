/**
 * Applique les restrictions de saisie (majuscumles et limite de longueur) à un champ de saisie.
 * @param {HTMLElement} inputElement L'élément de champ de saisie.
 * @param {number} maxLength La longueur maximale autorisée.
 */
export function applyInputRestrictions(inputElement: HTMLInputElement, maxLength: number) {
    inputElement.addEventListener('input', () => {
        inputElement.value = inputElement.value.toUpperCase();
        if (inputElement.value.length > maxLength) {
            inputElement.value = inputElement.value.slice(0, maxLength);
        }
    });
}

/**
 * Utilitaire pour "débouncer" une fonction (retarder son exécution).
 */
export function debounce(func: Function, delay: number) {
    let timeout: number;
    return function(this: any, ...args: any[]) {
        const context = this;
        clearTimeout(timeout);
        timeout = window.setTimeout(() => func.apply(context, args), delay);
    };
}
