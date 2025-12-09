/**
 * Utilitaires pour la gestion des formulaires
 */

/**
 * Options pour les restrictions de saisie
 */
export interface InputRestrictionOptions {
    uppercase?: boolean;
    lowercase?: boolean;
    trim?: boolean;
    allowedChars?: RegExp;
}

/**
 * Applique les restrictions de saisie à un champ de saisie
 * @param inputElement - L'élément de champ de saisie
 * @param maxLength - La longueur maximale autorisée
 * @param options - Options de restriction (uppercase, trim, etc.)
 * 
 * @example
 * applyInputRestrictions(input, 50, { uppercase: true, trim: true });
 */
export function applyInputRestrictions(
    inputElement: HTMLInputElement,
    maxLength: number,
    options: InputRestrictionOptions = { uppercase: true }
): void {
    if (!inputElement) {
        console.error('applyInputRestrictions: inputElement is required');
        return;
    }

    if (maxLength <= 0) {
        console.error('applyInputRestrictions: maxLength must be positive');
        return;
    }

    inputElement.addEventListener('input', () => {
        let value = inputElement.value;

        // Appliquer les transformations
        if (options.uppercase) {
            value = value.toUpperCase();
        } else if (options.lowercase) {
            value = value.toLowerCase();
        }

        if (options.trim) {
            value = value.trim();
        }

        // Filtrer les caractères non autorisés
        if (options.allowedChars) {
            value = value.split('').filter(char => options.allowedChars!.test(char)).join('');
        }

        // Limiter la longueur
        if (value.length > maxLength) {
            value = value.slice(0, maxLength);
        }

        inputElement.value = value;
    });
}

/**
 * Utilitaire pour "débouncer" une fonction (retarder son exécution)
 * Utile pour limiter les appels API lors de la saisie
 * 
 * @param func - La fonction à débouncer
 * @param delay - Le délai en millisecondes
 * @returns La fonction débouncée
 * 
 * @example
 * const debouncedSearch = debounce(searchAPI, 500);
 * input.addEventListener('input', () => debouncedSearch(input.value));
 */
export function debounce<T extends (...args: any[]) => any>(
    func: T,
    delay: number
): (...args: Parameters<T>) => void {
    if (typeof func !== 'function') {
        throw new TypeError('debounce: func must be a function');
    }

    if (delay < 0) {
        throw new RangeError('debounce: delay must be non-negative');
    }

    let timeout: number | undefined;

    return function (this: any, ...args: Parameters<T>): void {
        const context = this;

        if (timeout !== undefined) {
            clearTimeout(timeout);
        }

        timeout = window.setTimeout(() => {
            func.apply(context, args);
            timeout = undefined;
        }, delay);
    };
}

/**
 * Utilitaire pour "throttler" une fonction (limiter la fréquence d'exécution)
 * Utile pour limiter les événements de scroll ou resize
 * 
 * @param func - La fonction à throttler
 * @param limit - L'intervalle minimum entre les exécutions en millisecondes
 * @returns La fonction throttlée
 * 
 * @example
 * const throttledScroll = throttle(handleScroll, 100);
 * window.addEventListener('scroll', throttledScroll);
 */
export function throttle<T extends (...args: any[]) => any>(
    func: T,
    limit: number
): (...args: Parameters<T>) => void {
    if (typeof func !== 'function') {
        throw new TypeError('throttle: func must be a function');
    }

    if (limit < 0) {
        throw new RangeError('throttle: limit must be non-negative');
    }

    let inThrottle: boolean = false;
    let lastResult: ReturnType<T>;

    return function (this: any, ...args: Parameters<T>): void {
        const context = this;

        if (!inThrottle) {
            lastResult = func.apply(context, args);
            inThrottle = true;

            setTimeout(() => {
                inThrottle = false;
            }, limit);
        }
    };
}

/**
 * Valide un email
 * @param email - L'email à valider
 * @returns true si l'email est valide
 */
export function isValidEmail(email: string): boolean {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Valide un numéro de téléphone (format international ou local)
 * @param phone - Le numéro de téléphone à valider
 * @returns true si le numéro est valide
 */
export function isValidPhone(phone: string): boolean {
    // Accepte les formats: +261 34 12 345 67, 034 12 345 67, 0341234567
    const phoneRegex = /^(\+?\d{1,3}[-.\s]?)?\d{2,3}[-.\s]?\d{2}[-.\s]?\d{3}[-.\s]?\d{2}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

/**
 * Formate un nombre avec des séparateurs de milliers
 * @param value - Le nombre à formater
 * @param locale - La locale à utiliser (défaut: 'fr-FR')
 * @returns Le nombre formaté
 */
export function formatNumber(value: number, locale: string = 'fr-FR'): string {
    return new Intl.NumberFormat(locale).format(value);
}

/**
 * Nettoie une chaîne de caractères (trim + suppression des espaces multiples)
 * @param str - La chaîne à nettoyer
 * @returns La chaîne nettoyée
 */
export function sanitizeString(str: string): string {
    return str.trim().replace(/\s+/g, ' ');
}
