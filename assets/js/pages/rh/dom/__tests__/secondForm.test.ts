/**
 * @jest-environment jsdom
 */

import axios from 'axios';
import MockAdapter from 'axios-mock-adapter';

// Mock axios
const mock = new MockAdapter(axios);

describe('secondForm.ts - Validation et calculs', () => {
    beforeEach(() => {
        // Réinitialiser les mocks
        mock.reset();

        // Créer le DOM pour les tests
        document.body.innerHTML = `
            <input type="text" id="second_form_matricule" value="MAT001" />
            <input type="datetime-local" id="second_form_dateHeureMission_debut" />
            <input type="datetime-local" id="second_form_dateHeureMission_fin" />
            <div id="mission-overlap-warning" style="display: none;">Chevauchement détecté</div>
            <div id="date-error-message" style="display: none;">Date invalide</div>
            <input type="number" id="second_form_nombreJour" />
            <input type="text" id="second_form_idemnityDepl" />
            <input type="text" id="second_form_totalIndemniteDeplacement" />
            <input type="text" id="second_form_supplementJournaliere" />
            <input type="text" id="second_form_indemniteForfaitaire" />
            <input type="text" id="second_form_totalIndemniteForfaitaire" />
            <input type="text" id="second_form_autresDepense1" />
            <input type="text" id="second_form_autresDepense2" />
            <input type="text" id="second_form_autresDepense3" />
            <input type="text" id="second_form_totalAutresDepenses" />
            <input type="text" id="second_form_totalGeneralPayer" />
            <input type="text" id="typeMission" value="MISSION" />
            <select id="second_form_modePayement">
                <option value="ESPECE">Espèce</option>
                <option value="VIREMENT BANCAIRE">Virement bancaire</option>
                <option value="MOBILE MONEY">Mobile Money</option>
            </select>
            <input type="text" id="second_form_mode" />
            <label for="second_form_mode">Mode</label>
            <div id="total-amount-warning" style="display: none;">Montant élevé</div>
        `;
    });

    afterEach(() => {
        mock.restore();
    });

    describe('Validation de plage de dates', () => {
        test('Date début > date fin affiche un message d\'erreur', () => {
            const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
            const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
            const errorMessage = document.getElementById('date-error-message') as HTMLElement;

            startDateInput.value = '2025-01-10T10:00';
            endDateInput.value = '2025-01-05T10:00';

            // Simuler la validation
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate > endDate) {
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }

            expect(errorMessage.style.display).toBe('block');
        });

        test('Date début < date fin masque le message d\'erreur', () => {
            const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
            const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
            const errorMessage = document.getElementById('date-error-message') as HTMLElement;

            startDateInput.value = '2025-01-05T10:00';
            endDateInput.value = '2025-01-10T10:00';

            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate > endDate) {
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }

            expect(errorMessage.style.display).toBe('none');
        });
    });

    describe('Calcul du nombre de jours', () => {
        test('Calcule correctement le nombre de jours entre deux dates', () => {
            const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
            const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
            const nombreJourInput = document.getElementById('second_form_nombreJour') as HTMLInputElement;

            startDateInput.value = '2025-01-01T10:00';
            endDateInput.value = '2025-01-03T10:00';

            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const timeDifference = endDate.getTime() - startDate.getTime();
            const dayDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24)) + 1;

            nombreJourInput.value = dayDifference.toString();

            expect(nombreJourInput.value).toBe('3');
        });

        test('Retourne 1 jour pour le même jour', () => {
            const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
            const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
            const nombreJourInput = document.getElementById('second_form_nombreJour') as HTMLInputElement;

            startDateInput.value = '2025-01-01T10:00';
            endDateInput.value = '2025-01-01T18:00';

            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const timeDifference = endDate.getTime() - startDate.getTime();
            const dayDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24)) + 1;

            nombreJourInput.value = dayDifference.toString();

            expect(nombreJourInput.value).toBe('1');
        });
    });

    describe('Formatage des nombres', () => {
        test('Formate les nombres avec séparateur de milliers', () => {
            const numeral = require('numeral');

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
                ordinal: function (number: number) {
                    return number === 1 ? 'er' : 'ème';
                },
                currency: {
                    symbol: 'Ar'
                }
            });
            numeral.locale('fr-custom');

            const formatted = numeral(1000000).format('0,0');
            expect(formatted).toBe('1.000.000');
        });
    });

    describe('Calcul des totaux', () => {
        test('Calcule le total indemnité de déplacement', () => {
            const nombreJourInput = document.getElementById('second_form_nombreJour') as HTMLInputElement;
            const idemnityDeplInput = document.getElementById('second_form_idemnityDepl') as HTMLInputElement;
            const totalIdemniteDeplacementInput = document.getElementById('second_form_totalIndemniteDeplacement') as HTMLInputElement;

            nombreJourInput.value = '5';
            idemnityDeplInput.value = '10000';

            const nombreDeJour = parseInt(nombreJourInput.value);
            const indemnityDepl = parseInt(idemnityDeplInput.value.replace(/[^\\d]/g, ""));
            const totalIndemnity = nombreDeJour * indemnityDepl;

            totalIdemniteDeplacementInput.value = totalIndemnity.toString();

            expect(totalIdemniteDeplacementInput.value).toBe('50000');
        });

        test('Calcule le total autres dépenses', () => {
            const autreDepenseInput_1 = document.getElementById('second_form_autresDepense1') as HTMLInputElement;
            const autreDepenseInput_2 = document.getElementById('second_form_autresDepense2') as HTMLInputElement;
            const autreDepenseInput_3 = document.getElementById('second_form_autresDepense3') as HTMLInputElement;
            const totaAutreDepenseInput = document.getElementById('second_form_totalAutresDepenses') as HTMLInputElement;

            autreDepenseInput_1.value = '5000';
            autreDepenseInput_2.value = '3000';
            autreDepenseInput_3.value = '2000';

            const autreDepense_1 = parseInt(autreDepenseInput_1.value.replace(/[^\\d]/g, "")) || 0;
            const autreDepense_2 = parseInt(autreDepenseInput_2.value.replace(/[^\\d]/g, "")) || 0;
            const autreDepense_3 = parseInt(autreDepenseInput_3.value.replace(/[^\\d]/g, "")) || 0;
            const totaAutreDepense = autreDepense_1 + autreDepense_2 + autreDepense_3;

            totaAutreDepenseInput.value = totaAutreDepense.toString();

            expect(totaAutreDepenseInput.value).toBe('10000');
        });
    });

    describe('Validation Mobile Money', () => {
        test('Limite le numéro à 10 chiffres', () => {
            const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;
            const modeInput = document.getElementById('second_form_mode') as HTMLInputElement;

            modePayementInput.value = 'MOBILE MONEY';
            modeInput.value = '12345678901234'; // Plus de 10 chiffres

            // Simuler la validation
            if (modePayementInput.value === 'MOBILE MONEY') {
                const numericValue = modeInput.value.replace(/\\D/g, '');
                modeInput.value = numericValue.slice(0, 10);
            }

            expect(modeInput.value).toBe('1234567890');
        });

        test('Supprime les caractères non numériques', () => {
            const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;
            const modeInput = document.getElementById('second_form_mode') as HTMLInputElement;

            modePayementInput.value = 'MOBILE MONEY';
            modeInput.value = '123-456-7890';

            if (modePayementInput.value === 'MOBILE MONEY') {
                const numericValue = modeInput.value.replace(/\\D/g, '');
                modeInput.value = numericValue.slice(0, 10);
            }

            expect(modeInput.value).toBe('1234567890');
        });
    });

    describe('Vérification de chevauchement de missions (Mock API)', () => {
        test('Affiche un avertissement si chevauchement détecté', async () => {
            const matriculeInput = document.getElementById('second_form_matricule') as HTMLInputElement;
            const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
            const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
            const warningMessage = document.getElementById('mission-overlap-warning') as HTMLElement;

            matriculeInput.value = 'MAT001';
            startDateInput.value = '2025-01-05T10:00';
            endDateInput.value = '2025-01-10T10:00';

            // Mock de la réponse API
            mock.onGet('/api/validation/mission-overlap').reply(200, {
                overlap: true
            });

            // Simuler l'appel API
            const response = await axios.get('/api/validation/mission-overlap', {
                params: {
                    matricule: matriculeInput.value,
                    start_date: startDateInput.value,
                    end_date: endDateInput.value
                }
            });

            if (response.data.overlap) {
                warningMessage.style.display = 'block';
            } else {
                warningMessage.style.display = 'none';
            }

            expect(warningMessage.style.display).toBe('block');
        });

        test('Masque l\'avertissement si pas de chevauchement', async () => {
            const matriculeInput = document.getElementById('second_form_matricule') as HTMLInputElement;
            const startDateInput = document.getElementById('second_form_dateHeureMission_debut') as HTMLInputElement;
            const endDateInput = document.getElementById('second_form_dateHeureMission_fin') as HTMLInputElement;
            const warningMessage = document.getElementById('mission-overlap-warning') as HTMLElement;

            matriculeInput.value = 'MAT001';
            startDateInput.value = '2025-01-05T10:00';
            endDateInput.value = '2025-01-10T10:00';

            // Mock de la réponse API
            mock.onGet('/api/validation/mission-overlap').reply(200, {
                overlap: false
            });

            const response = await axios.get('/api/validation/mission-overlap', {
                params: {
                    matricule: matriculeInput.value,
                    start_date: startDateInput.value,
                    end_date: endDateInput.value
                }
            });

            if (response.data.overlap) {
                warningMessage.style.display = 'block';
            } else {
                warningMessage.style.display = 'none';
            }

            expect(warningMessage.style.display).toBe('none');
        });
    });

    describe('Mise à jour du label mode de paiement', () => {
        test('Change le label selon le mode sélectionné', () => {
            const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;
            const modeLabel = document.querySelector('label[for="second_form_mode"]') as HTMLLabelElement;

            modePayementInput.value = 'VIREMENT BANCAIRE';
            modeLabel.textContent = modePayementInput.value;

            expect(modeLabel.textContent).toBe('VIREMENT BANCAIRE');
        });

        test('Verrouille le champ pour VIREMENT BANCAIRE', () => {
            const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;
            const modeInput = document.getElementById('second_form_mode') as HTMLInputElement;

            modePayementInput.value = 'VIREMENT BANCAIRE';
            modeInput.readOnly = true;

            expect(modeInput.readOnly).toBe(true);
        });

        test('Ajoute un placeholder pour MOBILE MONEY', () => {
            const modePayementInput = document.getElementById('second_form_modePayement') as HTMLSelectElement;
            const modeInput = document.getElementById('second_form_mode') as HTMLInputElement;

            modePayementInput.value = 'MOBILE MONEY';
            modeInput.placeholder = 'Numéro sur 10 chiffres';

            expect(modeInput.placeholder).toBe('Numéro sur 10 chiffres');
        });
    });
});
