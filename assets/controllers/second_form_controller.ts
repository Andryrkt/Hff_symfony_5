import { Controller } from "@hotwired/stimulus";
import numeral from 'numeral';
import axios, { AxiosError } from 'axios';
import '../styles/pages/secondForm.scss';
import { initAgenceServiceHandlers } from '../js/utils/AgenceServiceManager';
import { applyInputRestrictions, debounce } from '../js/utils/form_utils';
import { FORM_CONSTANTS } from '../js/config/formConstants';

// --- Types ---

interface MissionOverlapResponse {
    overlap: boolean;
    conflictingMissions?: Array<{
        id: number;
        startDate: string;
        endDate: string;
    }>;
}

interface CodeBancaireResponse {
    codeBancaire: string;
}

interface IndemniteForfaitaireResponse {
    montant: string;
}

export default class extends Controller {
    connect() {
        this.initAgenceServiceHandlers();
        this.initDateValidation();
        this.initIndemniteForfaitaireUpdate();
        this.initInputValidation();
        this.initModeLabelUpdate();
        this.initTotalCalculations();
        this.initEmployeeFieldSwitching();
    }

    initAgenceServiceHandlers() {
        initAgenceServiceHandlers();
    }

    // --- Utilitaires de validation ---

    /**
     * Valide qu'une valeur est un nombre positif
     * @param value - Valeur à valider
     * @returns Nombre positif ou null si invalide
     */
    validatePositiveNumber(value: string): number | null {
        const num = parseInt(value.replace(/[^\d]/g, ""));
        return !isNaN(num) && num >= 0 ? num : null;
    }

    /**
     * Vérifie si un élément HTML existe
     * @param element - Élément à vérifier
     * @param elementName - Nom de l'élément pour le log
     * @returns true si l'élément existe
     */
    validateElement(element: HTMLElement | null, elementName: string): element is HTMLElement {
        if (!element) {
            console.error(`Élément manquant: ${elementName}`);
            return false;
        }
        return true;
    }

    // --- Logique de validation ---

    /**
     * Vérifie le chevauchement de mission via l'API avec gestion d'erreurs robuste
     */
    async checkMissionOverlap(): Promise<void> {
        const elements = {
            matricule: document.getElementById('second_form_matricule') as HTMLInputElement,
            startDate: document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement,
            endDate: document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement,
            warning: document.getElementById('mission-overlap-warning') as HTMLElement,
        };

        // Validation des éléments
        if (!this.validateElement(elements.matricule, 'matricule') ||
            !this.validateElement(elements.startDate, 'startDate') ||
            !this.validateElement(elements.endDate, 'endDate') ||
            !this.validateElement(elements.warning, 'warning')) {
            return;
        }

        const { matricule, startDate, endDate, warning } = elements;

        // Ne lance la vérification que si tous les champs sont remplis
        if (!matricule.value || !startDate.value || !endDate.value) {
            warning.style.display = 'none';
            return;
        }

        try {
            const response = await axios.get<MissionOverlapResponse>(
                FORM_CONSTANTS.API_ENDPOINTS.MISSION_OVERLAP,
                {
                    params: {
                        matricule: matricule.value,
                        start_date: startDate.value,
                        end_date: endDate.value
                    },
                    timeout: FORM_CONSTANTS.API_TIMEOUT,
                }
            );

            warning.style.display = response.data.overlap ? 'block' : 'none';

            if (response.data.overlap && response.data.conflictingMissions) {
                console.info('Missions en conflit:', response.data.conflictingMissions);
            }
        } catch (error) {
            this.handleApiError(error, 'vérification du chevauchement');
            warning.style.display = 'none';
        }
    }

    /**
     * Gère les erreurs API de manière centralisée
     * @param error - Erreur capturée
     * @param context - Contexte de l'erreur
     */
    handleApiError(error: unknown, context: string): void {
        if (axios.isAxiosError(error)) {
            const axiosError = error as AxiosError;

            if (axiosError.code === 'ECONNABORTED') {
                console.error(`Timeout lors de la ${context}`);
            } else if (axiosError.response) {
                console.error(`Erreur serveur lors de la ${context}:`, axiosError.response.status);
            } else if (axiosError.request) {
                console.error(`Pas de réponse du serveur lors de la ${context}`);
            } else {
                console.error(`Erreur lors de la ${context}:`, axiosError.message);
            }
        } else {
            console.error(`Erreur inattendue lors de la ${context}:`, error);
        }
    }

    /**
     * Compare la date de début et de fin et affiche un message d'erreur si nécessaire
     */
    validateDateRange(): void {
        const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
        const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
        const errorMessage = document.getElementById('date-error-message') as HTMLElement;

        if (!this.validateElement(startDateInput, 'startDate') ||
            !this.validateElement(endDateInput, 'endDate') ||
            !this.validateElement(errorMessage, 'errorMessage')) {
            return;
        }

        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate > endDate) {
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        } else {
            errorMessage.style.display = 'none';
        }

        // Déclenche la vérification de chevauchement (avec debounce)
        this.debouncedCheckMissionOverlap();
        this.calculateNumberOfDays();
    }

    /**
     * Calcule le nombre de jours entre deux dates
     * @param startDate - Date de début
     * @param endDate - Date de fin
     * @returns Nombre de jours (>= 0)
     */
    calculateDaysBetween(startDate: Date, endDate: Date): number {
        const timeDifference = endDate.getTime() - startDate.getTime();
        const dayDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24)) + 1;
        return dayDifference >= 0 ? dayDifference : 0;
    }

    /**
     * Calcule le nombre de jours entre deux dates et met à jour le champ 'nombreJour'
     */
    calculateNumberOfDays(): void {
        const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
        const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
        const nombreJourInput = document.getElementById('second_form_nombreJour') as HTMLInputElement;

        if (!this.validateElement(startDateInput, 'startDate') ||
            !this.validateElement(endDateInput, 'endDate') ||
            !this.validateElement(nombreJourInput, 'nombreJour')) {
            return;
        }

        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const dayDifference = this.calculateDaysBetween(startDate, endDate);

            nombreJourInput.value = dayDifference.toString();
        } else {
            nombreJourInput.value = '';
        }

        // Déclenche manuellement l'événement 'input' pour que les calculs dépendants s'exécutent
        const event = new Event('input', { bubbles: true });
        nombreJourInput.dispatchEvent(event);
    }

    // Crée une version "debounced" de la fonction de vérification
    debouncedCheckMissionOverlap = debounce(this.checkMissionOverlap.bind(this), FORM_CONSTANTS.DEBOUNCE_DELAY);

    /**
     * Initialise les écouteurs d'événements pour la validation des dates
     */
    initDateValidation(): void {
        const matriculeInput = document.getElementById('second_form_matricule');
        const startDateInput = document.getElementById('second_form_dateHeureMission_debut');
        const endDateInput = document.getElementById('second_form_dateHeureMission_fin');

        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', this.validateDateRange.bind(this));
            endDateInput.addEventListener('change', this.validateDateRange.bind(this));
        }

        if (matriculeInput) {
            matriculeInput.addEventListener('change', this.debouncedCheckMissionOverlap);
        }
    }

    /**
     * Initialise les calculs des totaux
     */
    initTotalCalculations(): void {
        const nombreJourInput = document.getElementById('second_form_nombreJour') as HTMLInputElement;
        const totalIdemniteDeplacementInput = document.getElementById('second_form_totalIndemniteDeplacement') as HTMLInputElement;
        const idemnityDeplInput = document.getElementById('second_form_idemnityDepl') as HTMLInputElement;
        const supplementJournalierInput = document.getElementById('second_form_supplementJournaliere') as HTMLInputElement;
        const indemniteForfaitaireJournaliereInput = document.getElementById('second_form_indemniteForfaitaire') as HTMLInputElement;
        const totalindemniteForfaitaireInput = document.getElementById('second_form_totalIndemniteForfaitaire') as HTMLInputElement;
        const autreDepenseInput_1 = document.getElementById('second_form_autresDepense1') as HTMLInputElement;
        const autreDepenseInput_2 = document.getElementById('second_form_autresDepense2') as HTMLInputElement;
        const autreDepenseInput_3 = document.getElementById('second_form_autresDepense3') as HTMLInputElement;
        const totaAutreDepenseInput = document.getElementById('second_form_totalAutresDepenses') as HTMLInputElement;
        const montantTotalInput = document.getElementById('second_form_totalGeneralPayer') as HTMLInputElement;
        const sousTypeDocInput = document.getElementById('typeMission') as HTMLInputElement;

        // --- Numeral.js Configuration ---
        if (!numeral.locales['fr-custom']) {
            numeral.register('locale', 'fr-custom', {
                delimiters: {
                    thousands: '.',
                    decimal: ','
                },
                abbreviations: {
                    thousand: 'k',
                    million: 'm',
                    billion: 'b',
                    trillion: 't'
                },
                ordinal: function (number) {
                    return number === 1 ? 'er' : 'ème';
                },
                currency: {
                    symbol: 'Ar'
                }
            });
        }
        numeral.locale('fr-custom');

        const formatNumberInt = (value: string | number): string => {
            return numeral(value).format('0,0');
        }

        // --- Total Indemnité de Déplacement ---
        const updateTotalIndemnity = (): void => {
            const nombreDeJour = this.validatePositiveNumber(nombreJourInput.value);
            const indemnityDepl = this.validatePositiveNumber(idemnityDeplInput.value);

            if (nombreDeJour !== null && indemnityDepl !== null) {
                const totalIndemnity = nombreDeJour * indemnityDepl;
                totalIdemniteDeplacementInput.value = formatNumberInt(totalIndemnity);
                const event = new Event("valueAdded");
                totalIdemniteDeplacementInput.dispatchEvent(event);
            } else {
                totalIdemniteDeplacementInput.value = "";
            }
        }

        if (idemnityDeplInput) {
            idemnityDeplInput.addEventListener("input", () => {
                idemnityDeplInput.value = formatNumberInt(idemnityDeplInput.value);
                updateTotalIndemnity();
            });
        }

        // --- Total Indemnité Forfaitaire ---

        const calculTotalForfaitaire = (): void => {
            const nombreDeJour = this.validatePositiveNumber(nombreJourInput.value);
            const supplementJournalier = this.validatePositiveNumber(supplementJournalierInput.value);
            const indemniteForfaitaireJournaliere = this.validatePositiveNumber(indemniteForfaitaireJournaliereInput.value);

            if (nombreDeJour === null) {
                totalindemniteForfaitaireInput.value = "";
                return;
            }

            let total = 0;

            if (indemniteForfaitaireJournaliere !== null) {
                total += indemniteForfaitaireJournaliere;
            }

            if (supplementJournalier !== null) {
                total += supplementJournalier;
            }

            if (total > 0) {
                totalindemniteForfaitaireInput.value = formatNumberInt(nombreDeJour * total);
            } else {
                totalindemniteForfaitaireInput.value = "";
            }

            const event = new Event("valueAdded");
            totalindemniteForfaitaireInput.dispatchEvent(event);
        }

        const debouncedCalculTotalForfaitaire = debounce(calculTotalForfaitaire, 300);
        nombreJourInput.addEventListener("input", debouncedCalculTotalForfaitaire);

        supplementJournalierInput.addEventListener("input", () => {
            supplementJournalierInput.value = formatNumberInt(supplementJournalierInput.value);
            debouncedCalculTotalForfaitaire();
        });

        indemniteForfaitaireJournaliereInput.addEventListener("input", () => {
            indemniteForfaitaireJournaliereInput.value = formatNumberInt(indemniteForfaitaireJournaliereInput.value);
            debouncedCalculTotalForfaitaire();
        });

        // --- Total Autres Dépenses ---
        const calculTotalAutreDepense = (): void => {
            const autreDepense_1 = this.validatePositiveNumber(autreDepenseInput_1.value) || 0;
            const autreDepense_2 = this.validatePositiveNumber(autreDepenseInput_2.value) || 0;
            const autreDepense_3 = this.validatePositiveNumber(autreDepenseInput_3.value) || 0;

            const totaAutreDepense = autreDepense_1 + autreDepense_2 + autreDepense_3;
            totaAutreDepenseInput.value = formatNumberInt(totaAutreDepense);

            const event = new Event("valueAdded");
            totaAutreDepenseInput.dispatchEvent(event);
        }

        autreDepenseInput_1.addEventListener("input", () => {
            autreDepenseInput_1.value = formatNumberInt(autreDepenseInput_1.value);
            calculTotalAutreDepense();
        });
        autreDepenseInput_2.addEventListener("input", () => {
            autreDepenseInput_2.value = formatNumberInt(autreDepenseInput_2.value);
            calculTotalAutreDepense();
        });
        autreDepenseInput_3.addEventListener("input", () => {
            autreDepenseInput_3.value = formatNumberInt(autreDepenseInput_3.value);
            calculTotalAutreDepense();
        });

        // --- Calcul Montant Total ---
        const calculTotal = (): void => {
            const totaAutreDepense = this.validatePositiveNumber(totaAutreDepenseInput.value) || 0;
            const totalIdemniteDeplacement = this.validatePositiveNumber(totalIdemniteDeplacementInput.value) || 0;
            const totalindemniteForfaitaire = this.validatePositiveNumber(totalindemniteForfaitaireInput.value) || 0;
            const totalAmountWarning = document.querySelector("#total-amount-warning") as HTMLElement;

            const montantTotal = totalindemniteForfaitaire + totaAutreDepense - totalIdemniteDeplacement;

            if (sousTypeDocInput.value === FORM_CONSTANTS.MISSION_TYPES.TROP_PERCU) {
                montantTotalInput.value = "-" + formatNumberInt(montantTotal);
                if (totalAmountWarning) totalAmountWarning.style.display = "none";
            } else if (sousTypeDocInput.value !== FORM_CONSTANTS.MISSION_TYPES.FRAIS_EXCEPTIONNEL &&
                montantTotal > FORM_CONSTANTS.MAX_AMOUNT_WARNING) {
                if (totalAmountWarning) totalAmountWarning.style.display = "block";
                montantTotalInput.value = formatNumberInt(montantTotal);
            } else {
                montantTotalInput.value = formatNumberInt(montantTotal);
                if (totalAmountWarning) totalAmountWarning.style.display = "none";
            }
        }

        totalIdemniteDeplacementInput.addEventListener("valueAdded", calculTotal);
        totalindemniteForfaitaireInput.addEventListener("valueAdded", calculTotal);
        totaAutreDepenseInput.addEventListener("valueAdded", calculTotal);
    }

    /**
     * Gère la validation en temps réel pour le champ 'mode' lorsque 'MOBILE MONEY' est sélectionné
     */
    handleModeInput(event: Event): void {
        const input = event.target as HTMLInputElement;
        const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;

        if (modePayementInput && modePayementInput.value === FORM_CONSTANTS.PAYMENT_MODES.MOBILE_MONEY) {
            const numericValue = input.value.replace(/\D/g, '');
            input.value = numericValue.slice(0, FORM_CONSTANTS.MOBILE_MONEY_MAX_DIGITS);
        }
    }

    /**
     * Initialise la mise à jour dynamique du label du champ 'mode' en fonction du 'modePayement'
     */
    initModeLabelUpdate(): void {
        const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;
        if (modePayementInput) {
            modePayementInput.addEventListener('change', this.updateModeLabel.bind(this));
            this.updateModeLabel();
        }

        const modeInput = document.getElementById('second_form_mode') as HTMLInputElement;
        if (modeInput) {
            modeInput.addEventListener('input', this.handleModeInput.bind(this));
        }
    }

    /**
     * Met à jour le label du champ 'mode' en fonction de la valeur sélectionnée dans 'modePayement'
     */
    async updateModeLabel(): Promise<void> {
        const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;
        const modeLabel = document.querySelector('label[for="second_form_mode"]') as HTMLLabelElement;
        const modeInput = document.getElementById("second_form_mode") as HTMLInputElement;

        if (!this.validateElement(modePayementInput, 'modePayement') ||
            !this.validateElement(modeLabel, 'modeLabel') ||
            !this.validateElement(modeInput, 'modeInput')) {
            return;
        }

        const selectedModePayement = modePayementInput.value;
        modeLabel.textContent = selectedModePayement;

        // Réinitialiser l'état du champ 'mode'
        modeInput.readOnly = false;
        modeInput.placeholder = '';

        if (selectedModePayement === FORM_CONSTANTS.PAYMENT_MODES.VIREMENT) {
            modeInput.readOnly = true;
            const matriculeInput = document.getElementById('second_form_matricule') as HTMLSelectElement;

            if (matriculeInput && matriculeInput.value) {
                try {
                    const response = await axios.get<CodeBancaireResponse>(
                        FORM_CONSTANTS.API_ENDPOINTS.CODE_BANCAIRE,
                        {
                            params: { matricule: matriculeInput.value },
                            timeout: FORM_CONSTANTS.API_TIMEOUT,
                        }
                    );
                    modeInput.value = response.data.codeBancaire;
                } catch (error) {
                    this.handleApiError(error, 'récupération du code bancaire');
                    modeInput.value = '';
                }
            }
        } else if (selectedModePayement === FORM_CONSTANTS.PAYMENT_MODES.MOBILE_MONEY) {
            modeInput.value = '';
            modeInput.placeholder = `Numéro sur ${FORM_CONSTANTS.MOBILE_MONEY_MAX_DIGITS} chiffres`;
        } else {
            modeInput.value = '';
        }
    }

    /**
     * Initialise la validation des champs de saisie
     */
    initInputValidation(): void {
        for (const [id, maxLength] of Object.entries(FORM_CONSTANTS.FIELD_MAX_LENGTHS)) {
            const inputElement = document.getElementById(`second_form_${id}`) as HTMLInputElement;
            if (inputElement) {
                applyInputRestrictions(inputElement, maxLength);
            }
        }
    }

    /**
     * Initialise la mise à jour du champ indemniteForfaitaire en fonction du site
     */
    initIndemniteForfaitaireUpdate(): void {
        const siteInput = document.getElementById('second_form_site');
        if (siteInput) {
            siteInput.addEventListener('change', this.updateIndemniteForfaitaire.bind(this));
        }
    }

    /**
     * Met à jour le champ indemniteForfaitaire en appelant l'API
     */
    async updateIndemniteForfaitaire(): Promise<void> {
        const typeMissionInput = document.getElementById('second_form_typeMission') as HTMLInputElement;
        const categorieInput = document.getElementById('second_form_categorie') as HTMLInputElement;
        const rmqInput = document.getElementById('rmq') as HTMLInputElement;
        const siteInput = document.getElementById('second_form_site') as HTMLInputElement;
        const indemniteForfaitaireInput = document.getElementById('second_form_indemniteForfaitaire') as HTMLInputElement;

        if (!this.validateElement(typeMissionInput, 'typeMission') ||
            !this.validateElement(categorieInput, 'categorie') ||
            !this.validateElement(siteInput, 'site') ||
            !this.validateElement(indemniteForfaitaireInput, 'indemniteForfaitaire')) {
            return;
        }

        const typeMissionId = typeMissionInput.value;
        const categorieId = categorieInput.value;
        const rmqId = rmqInput?.value;
        const siteId = siteInput.value;

        if (typeMissionId && categorieId && siteId) {
            try {
                const response = await axios.get<IndemniteForfaitaireResponse>(
                    FORM_CONSTANTS.API_ENDPOINTS.INDEMNITE_FORFAITAIRE,
                    {
                        params: {
                            typeMission: typeMissionId,
                            categorie: categorieId,
                            rmq: rmqId,
                            site: siteId
                        },
                        timeout: FORM_CONSTANTS.API_TIMEOUT,
                    }
                );

                indemniteForfaitaireInput.value = response.data.montant;
                const event = new Event('input', { bubbles: true });
                indemniteForfaitaireInput.dispatchEvent(event);
            } catch (error) {
                this.handleApiError(error, 'mise à jour de l\'indemnité forfaitaire');
                indemniteForfaitaireInput.value = '';
                const event = new Event('input', { bubbles: true });
                indemniteForfaitaireInput.dispatchEvent(event);
            }
        }
    }

    /**
     * Initialise le basculement d'affichage des champs matricule et cin
     */
    initEmployeeFieldSwitching(): void {
        const formContainer = document.getElementById('form-container');
        const matriculeContainer = document.getElementById('matricule-field-container');
        const cinContainer = document.getElementById('cin-field-container');

        if (!this.validateElement(formContainer, 'formContainer') ||
            !this.validateElement(matriculeContainer, 'matriculeContainer') ||
            !this.validateElement(cinContainer, 'cinContainer')) {
            return;
        }

        const salarierType = formContainer.dataset.salarierType;

        if (salarierType === FORM_CONSTANTS.EMPLOYEE_TYPES.PERMANENT) {
            matriculeContainer.style.display = '';
            cinContainer.style.display = 'none';
        } else {
            matriculeContainer.style.display = 'none';
            cinContainer.style.display = '';
        }
    }
}
