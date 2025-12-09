import { Controller } from "@hotwired/stimulus";
import '@styles/pages/secondForm.scss';
export default class extends Controller {
    connect(): void;
    initAgenceServiceHandlers(): void;
    /**
     * Valide qu'une valeur est un nombre positif
     * @param value - Valeur à valider
     * @returns Nombre positif ou null si invalide
     */
    validatePositiveNumber(value: string): number | null;
    /**
     * Vérifie si un élément HTML existe
     * @param element - Élément à vérifier
     * @param elementName - Nom de l'élément pour le log
     * @returns true si l'élément existe
     */
    validateElement(element: HTMLElement | null, elementName: string): element is HTMLElement;
    /**
     * Vérifie le chevauchement de mission via l'API avec gestion d'erreurs robuste
     */
    checkMissionOverlap(): Promise<void>;
    /**
     * Gère les erreurs API de manière centralisée
     * @param error - Erreur capturée
     * @param context - Contexte de l'erreur
     */
    handleApiError(error: unknown, context: string): void;
    /**
     * Compare la date de début et de fin et affiche un message d'erreur si nécessaire
     */
    validateDateRange(): void;
    /**
     * Calcule le nombre de jours entre deux dates
     * @param startDate - Date de début
     * @param endDate - Date de fin
     * @returns Nombre de jours (>= 0)
     */
    calculateDaysBetween(startDate: Date, endDate: Date): number;
    /**
     * Calcule le nombre de jours entre deux dates et met à jour le champ 'nombreJour'
     */
    calculateNumberOfDays(): void;
    debouncedCheckMissionOverlap: (...args: unknown[]) => void;
    /**
     * Initialise les écouteurs d'événements pour la validation des dates
     */
    initDateValidation(): void;
    /**
     * Initialise les calculs des totaux
     */
    initTotalCalculations(): void;
    /**
     * Gère la validation en temps réel pour le champ 'mode' lorsque 'MOBILE MONEY' est sélectionné
     */
    handleModeInput(event: Event): void;
    /**
     * Initialise la mise à jour dynamique du label du champ 'mode' en fonction du 'modePayement'
     */
    initModeLabelUpdate(): void;
    /**
     * Met à jour le label du champ 'mode' en fonction de la valeur sélectionnée dans 'modePayement'
     */
    updateModeLabel(): Promise<void>;
    /**
     * Initialise la validation des champs de saisie
     */
    initInputValidation(): void;
    /**
     * Initialise la mise à jour du champ indemniteForfaitaire en fonction du site
     */
    initIndemniteForfaitaireUpdate(): void;
    /**
     * Met à jour le champ indemniteForfaitaire en appelant l'API
     */
    updateIndemniteForfaitaire(): Promise<void>;
    /**
     * Initialise le basculement d'affichage des champs matricule et cin
     */
    initEmployeeFieldSwitching(): void;
}
//# sourceMappingURL=second_form_controller.d.ts.map