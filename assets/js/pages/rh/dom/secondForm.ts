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
});

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
}

/**
 * Met à jour le label du champ 'mode' en fonction de la valeur sélectionnée dans 'modePayement'.
 */
async function updateModeLabel(): Promise<void> {
    const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;
    const modeLabel = document.querySelector('label[for="second_form_mode"]') as HTMLLabelElement;

    if (!modePayementInput || !modeLabel) {
        return;
    }
    const selectedModePayement = modePayementInput.value;

    modeLabel.textContent = selectedModePayement;
console.log(selectedModePayement);

    if (selectedModePayement === 'VIREMENT BANCAIRE') {
        const matriculeInput = document.getElementById('second_form_matricule') as HTMLSelectElement;
        if(matriculeInput) {
            try {
                const matricule = matriculeInput.value;
                console.log(matricule);
                
                const response = await axios.get('/api/rh/dom/mode', {
                    params: {
                        matricule: matricule,
                    }
                });
                const modeInput = document.getElementById("second_form_mode") as HTMLSelectElement;
                modeInput.value =  response.data.codeBancaire;
            } catch (error) {
                console.error('Erreur lors de la recupéraiton du code bancaire de l\'utilisateur, voir l\'erreur :', error);
            }
        }
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
    const siteInput = document.getElementById('second_form_site') as HTMLInputElement;
    const indemniteForfaitaireInput = document.getElementById('second_form_indemniteForfaitaire') as HTMLInputElement;

    if (!typeMissionInput || !categorieInput || !siteInput || !indemniteForfaitaireInput) {
        return;
    }

    const typeMissionId = typeMissionInput.value;
    const categorieId = categorieInput.value;
    const siteId = siteInput.value;

    if (typeMissionId && categorieId && siteId) {
        try {
            const response = await axios.get('/api/rh/dom/indemnite-forfaitaire', {
                params: {
                    typeMission: typeMissionId,
                    categorie: categorieId,
                    site: siteId
                }
            });
            indemniteForfaitaireInput.value = response.data.montant;
        } catch (error) {
            console.error('Erreur lors de la mise à jour de l\'indemnité forfaitaire:', error);
            indemniteForfaitaireInput.value = '';
        }
    }
}
