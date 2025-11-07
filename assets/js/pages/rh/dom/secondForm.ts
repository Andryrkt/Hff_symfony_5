import numeral from 'numeral';

// assets/js/secondForm.js

import '../../../../styles/pages/secondForm.scss';
import { initAgenceServiceHandlers } from '../../../services/AgenceServiceManager';
import axios from 'axios';
import { applyInputRestrictions, debounce } from '../../../utils/form_utils';

// --- Logique de validation ---

/**
 * Vérifie le chevauchement de mission via l'API.
 */
async function checkMissionOverlap() {
    const matriculeInput = document.getElementById('second_form_matricule') as HTMLInputElement;
    const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
    const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
    const warningMessage = document.getElementById('mission-overlap-warning');

    if (!matriculeInput || !startDateInput || !endDateInput || !warningMessage) {
        return; // Ne rien faire si les champs n'existent pas
    }

    const matricule = matriculeInput.value;
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    // Ne lance la vérification que si tous les champs sont remplis
    if (matricule && startDate && endDate) {
        try {
            const response = await axios.get('/api/validation/mission-overlap', {
                params: {
                    matricule,
                    start_date: startDate,
                    end_date: endDate
                }
            });

            if (response.data.overlap) {
                warningMessage.style.display = 'block';
            } else {
                warningMessage.style.display = 'none';
            }
        } catch (error) {
            console.error('Erreur lors de la vérification du chevauchement:', error);
            warningMessage.style.display = 'none'; // Cacher en cas d'erreur API
        }
    } else {
        warningMessage.style.display = 'none'; // Cacher si un champ est vide
    }
}

/**
 * Compare la date de début et de fin et affiche un message d'erreur si nécessaire.
 */
function validateDateRange() {
    const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
    const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
    const errorMessage = document.getElementById('date-error-message');

    if (!startDateInput || !endDateInput || !errorMessage) {
        console.warn('Date validation elements not found.');
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
    debouncedCheckMissionOverlap();
    calculateNumberOfDays();
}

/**
 * Calcule le nombre de jours entre deux dates et met à jour le champ 'nombreJour'.
 */
function calculateNumberOfDays() {
    const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
    const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
    const nombreJourInput = document.getElementById('second_form_nombreJour') as HTMLInputElement;

    if (!startDateInput || !endDateInput || !nombreJourInput) {
        return;
    }

    if (startDateInput.value && endDateInput.value) {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        // Calcul de la différence en millisecondes
        const timeDifference = endDate.getTime() - startDate.getTime();

        // Conversion en jours
        // Ajout de 1 pour inclure le jour de fin
        const dayDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24)) + 1;

        if (dayDifference >= 0) {
            nombreJourInput.value = dayDifference.toString();
        } else {
            nombreJourInput.value = ''; // Ou gérer l'erreur si la date de fin est avant la date de début
        }
    } else {
        nombreJourInput.value = '';
    }

    // Déclenche manuellement l'événement 'input' pour que les calculs dépendants s'exécutent
    const event = new Event('input', { bubbles: true });
    nombreJourInput.dispatchEvent(event);
}

// Crée une version "debounced" de la fonction de vérification
const debouncedCheckMissionOverlap = debounce(checkMissionOverlap, 500); // Attend 500ms après la dernière frappe

/**
 * Initialise les écouteurs d'événements pour la validation des dates.
 * la date de debut ne doit pas supérieur à la date de fin
 */
function initDateValidation() {
    const matriculeInput = document.getElementById('second_form_matricule');
    const startDateInput = document.getElementById('second_form_dateHeureMission_debut');
    const endDateInput = document.getElementById('second_form_dateHeureMission_fin');

    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', validateDateRange);
        endDateInput.addEventListener('change', validateDateRange);
    }
    if (matriculeInput) {
        // On vérifie aussi si le matricule change (pour les formulaires dynamiques)
        matriculeInput.addEventListener('change', debouncedCheckMissionOverlap);
    }
}

// --- Initialisation principale ---

document.addEventListener('DOMContentLoaded', () => {
    initAgenceServiceHandlers();
    initDateValidation();
    initIndemniteForfaitaireUpdate();
    initInputValidation();
    initModeLabelUpdate();
    initTotalCalculations();
    initEmployeeFieldSwitching();
});

/**
 * Initialise les calculs des totaux.
 */
function initTotalCalculations() {
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
    numeral.locale('fr-custom');

    function formatNumberInt(value: string | number): string {
        return numeral(value).format('0,0');
    }

    // --- Total Indemnité de Déplacement ---
    function updateTotalIndemnity() {
        const nombreDeJour = parseInt(nombreJourInput.value);
        const indemnityDepl = parseInt(idemnityDeplInput.value.replace(/[^\d]/g, ""));

        if (!isNaN(nombreDeJour) && !isNaN(indemnityDepl)) {
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
    nombreJourInput.addEventListener("input", calculTotalForfaitaire);

    function calculTotalForfaitaire() {
        
        if (supplementJournalierInput.value === "" && indemniteForfaitaireJournaliereInput.value !== "") {
            const nombreDeJour = parseInt(nombreJourInput.value);
            const indemniteForfaitaireJournaliere = parseInt(indemniteForfaitaireJournaliereInput.value.replace(/[^\d]/g, ""));
            totalindemniteForfaitaireInput.value = formatNumberInt(nombreDeJour * indemniteForfaitaireJournaliere);
        } else if (supplementJournalierInput.value !== "" && indemniteForfaitaireJournaliereInput.value !== "") {
            const supplementJournalier = parseInt(supplementJournalierInput.value.replace(/[^\d]/g, ""));
            const nombreDeJour = parseInt(nombreJourInput.value);
            const indemniteForfaitaireJournaliere = parseInt(indemniteForfaitaireJournaliereInput.value.replace(/[^\d]/g, ""));
            totalindemniteForfaitaireInput.value = formatNumberInt(nombreDeJour * (indemniteForfaitaireJournaliere + supplementJournalier));
        } else if (supplementJournalierInput.value !== "") {
            const supplementJournalier = parseInt(supplementJournalierInput.value.replace(/[^\d]/g, ""));
            const nombreDeJour = parseInt(nombreJourInput.value);
            totalindemniteForfaitaireInput.value = formatNumberInt(nombreDeJour * supplementJournalier);
        }

        const event = new Event("valueAdded");
        totalindemniteForfaitaireInput.dispatchEvent(event);
    }

    supplementJournalierInput.addEventListener("input", () => {
        supplementJournalierInput.value = formatNumberInt(supplementJournalierInput.value);
        calculTotalForfaitaire();
    });

    indemniteForfaitaireJournaliereInput.addEventListener("input", () => {
        indemniteForfaitaireJournaliereInput.value = formatNumberInt(indemniteForfaitaireJournaliereInput.value);
        calculTotalForfaitaire();
    });

    // --- Total Autres Dépenses ---
    function calculTotalAutreDepense() {
        const autreDepense_1 = parseInt(autreDepenseInput_1.value.replace(/[^\d]/g, "")) || 0;
        const autreDepense_2 = parseInt(autreDepenseInput_2.value.replace(/[^\d]/g, "")) || 0;
        const autreDepense_3 = parseInt(autreDepenseInput_3.value.replace(/[^\d]/g, "")) || 0;
        let totaAutreDepense = autreDepense_1 + autreDepense_2 + autreDepense_3;
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
    function calculTotal() {
        const totaAutreDepense = parseInt(totaAutreDepenseInput.value.replace(/[^\d]/g, "")) || 0;
        const totalIdemniteDeplacement = parseInt(totalIdemniteDeplacementInput.value.replace(/[^\d]/g, "")) || 0;
        const totalindemniteForfaitaire = parseInt(totalindemniteForfaitaireInput.value.replace(/[^\d]/g, "")) || 0;

        let montantTotal = totalindemniteForfaitaire + totaAutreDepense - totalIdemniteDeplacement;

        if (sousTypeDocInput.value == 'TROP PERCU') {
            montantTotalInput.value = "-" + formatNumberInt(montantTotal);
        } else {
            montantTotalInput.value = formatNumberInt(montantTotal);
        }
    }

    totalIdemniteDeplacementInput.addEventListener("valueAdded", calculTotal);
    totalindemniteForfaitaireInput.addEventListener("valueAdded", calculTotal);
    totaAutreDepenseInput.addEventListener("valueAdded", calculTotal);
}

/**
 * Gère la validation en temps réel pour le champ 'mode' lorsque 'MOBILE MONEY' est sélectionné.
 */
function handleModeInput(event: Event) {
    const input = event.target as HTMLInputElement;
    const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;

    if (modePayementInput && modePayementInput.value === 'MOBILE MONEY') {
        // Remplacer tout ce qui n'est pas un chiffre
        const numericValue = input.value.replace(/\D/g, '');
        // Limiter à 10 chiffres et mettre à jour la valeur
        input.value = numericValue.slice(0, 10);
    }
}

/**
 * Initialise la mise à jour dynamique du label du champ 'mode' en fonction du 'modePayement'.
 */
function initModeLabelUpdate() {
    const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;
    if (modePayementInput) {
        modePayementInput.addEventListener('change', updateModeLabel);
        // Appeler une fois au chargement pour définir le label initial
        updateModeLabel();
    }

    // Ajout de l'écouteur pour la validation du champ 'mode'
    const modeInput = document.getElementById('second_form_mode') as HTMLInputElement;
    if (modeInput) {
        modeInput.addEventListener('input', handleModeInput);
    }
}

/**
 * Met à jour le label du champ 'mode' en fonction de la valeur sélectionnée dans 'modePayement'.
 */
async function updateModeLabel(): Promise<void> {
    const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;
    const modeLabel = document.querySelector('label[for="second_form_mode"]') as HTMLLabelElement;
    const modeInput = document.getElementById("second_form_mode") as HTMLInputElement;

    if (!modePayementInput || !modeLabel || !modeInput) {
        return;
    }
    const selectedModePayement = modePayementInput.value;

    modeLabel.textContent = selectedModePayement;

    // --- Réinitialiser l'état du champ 'mode' ---
    modeInput.readOnly = false;
    modeInput.placeholder = '';

    if (selectedModePayement === 'VIREMENT BANCAIRE') {
        modeInput.readOnly = true; // Verrouiller le champ
        const matriculeInput = document.getElementById('second_form_matricule') as HTMLSelectElement;
        if(matriculeInput) {
            try {
                const matricule = matriculeInput.value;
                
                const response = await axios.get('/api/rh/dom/mode', {
                    params: {
                        matricule: matricule,
                    }
                });
                modeInput.value =  response.data.codeBancaire;
            } catch (error) {
                console.error("Erreur lors de la recupéraiton du code bancaire de l'utilisateur, voir l'erreur :", error);
                modeInput.value = ''; // Effacer en cas d'erreur
            }
        }
    } else if (selectedModePayement === 'MOBILE MONEY') {
        modeInput.value = ''; // Effacer la valeur précédente
        modeInput.placeholder = 'Numéro sur 10 chiffres'; // Ajouter une indication
    } else {
        // Pour tous les autres modes, effacer le champ
        modeInput.value = '';
    }
}


/**
 * Initialise la validation des champs de saisie (limite de caractères et majuscules).
 */
function initInputValidation() {
    const fieldsToValidate: { [id: string]: number } = {
        'second_form_motifDeplacement': 60,
        'second_form_client': 30,
        'second_form_lieuIntervention': 60,
        'second_form_motifAutresDepense1': 30,
        'second_form_motifAutresDepense2': 30,
        'second_form_motifAutresDepense3': 30
    };

    for (const id in fieldsToValidate) {
        const inputElement = document.getElementById(id) as HTMLInputElement;
        if (inputElement) {
            applyInputRestrictions(inputElement, fieldsToValidate[id]);
        }
    }
}

/**
 * Initialise la mise à jour du champ indemniteForfaitaire en fonction du site.
 */
function initIndemniteForfaitaireUpdate() {
    const siteInput = document.getElementById('second_form_site');
    if (siteInput) {
        siteInput.addEventListener('change', updateIndemniteForfaitaire);
    }
}

/**
 * Met à jour le champ indemniteForfaitaire en appelant l'API.
 */
async function updateIndemniteForfaitaire() {
    const typeMissionInput = document.getElementById('second_form_typeMission') as HTMLInputElement;
    const categorieInput = document.getElementById('second_form_categorie') as HTMLInputElement;
    const rmqInput = document.getElementById('rmq') as HTMLInputElement;
    const siteInput = document.getElementById('second_form_site') as HTMLInputElement;
    const indemniteForfaitaireInput = document.getElementById('second_form_indemniteForfaitaire') as HTMLInputElement;

    if (!typeMissionInput || !categorieInput || !siteInput || !indemniteForfaitaireInput) {
        return;
    }

    const typeMissionId = typeMissionInput.value;
    const categorieId = categorieInput.value;
    const rmqId = rmqInput.value;
    const siteId = siteInput.value;

    if (typeMissionId && categorieId && siteId) {
        try {
            const response = await axios.get('/api/rh/dom/indemnite-forfaitaire', {
                params: {
                    typeMission: typeMissionId,
                    categorie: categorieId,
                    rmq: rmqId,
                    site: siteId
                }
            });
            indemniteForfaitaireInput.value = response.data.montant;

            // Déclenche l'événement input pour recalculer le total
            const event = new Event('input', { bubbles: true });
            indemniteForfaitaireInput.dispatchEvent(event);
        } catch (error) {
            console.error('Erreur lors de la mise à jour de l\'indemnité forfaitaire:', error);
            indemniteForfaitaireInput.value = '';

            // Déclenche également l'événement en cas d'erreur pour vider le total
            const event = new Event('input', { bubbles: true });
            indemniteForfaitaireInput.dispatchEvent(event);
        }
    }
}

/**
 * Initialise le basculement d'affichage des champs matricule et cin en fonction du type de salarié.
 */
function initEmployeeFieldSwitching() {
    const formContainer = document.getElementById('form-container');
    const matriculeContainer = document.getElementById('matricule-field-container');
    const cinContainer = document.getElementById('cin-field-container');

    if (!formContainer || !matriculeContainer || !cinContainer) {
        console.error('One or more required elements for employee field switching are missing.');
        return;
    }

    const salarierType = formContainer.dataset.salarierType;

    if (salarierType === 'PERMANENT') {
        matriculeContainer.style.display = ''; 
        cinContainer.style.display = 'none';
    } else {
        matriculeContainer.style.display = 'none';
        cinContainer.style.display = '';
    }
}
