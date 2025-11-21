/**
 * @jest-environment jsdom
 */

describe('firstForm.js - Gestion dynamique des champs', () => {
    let salarieSelect, interneDiv, externeDiv, matriculeInput;
    let typeMissionSelect, categorieFieldContainer, categorieInput;
    let matriculeNomSelect;

    beforeEach(() => {
        // Créer le DOM pour les tests
        document.body.innerHTML = `
            <select id="first_form_salarier">
                <option value="PERMANENT">Permanent</option>
                <option value="TEMPORAIRE">Temporaire</option>
            </select>
            <div id="Interne" style="display: block;">
                <select id="first_form_matriculeNom">
                    <option value="">Sélectionner</option>
                    <option value="1" data-matricule="MAT001">Jean Dupont</option>
                    <option value="2" data-matricule="MAT002">Marie Martin</option>
                </select>
                <input type="text" id="first_form_matricule" />
                <input type="text" id="first_form_nom" required />
                <input type="text" id="first_form_prenom" required />
            </div>
            <div id="externe" style="display: none;">
                <input type="text" id="first_form_cin" required disabled />
            </div>
            <select id="first_form_typeMission">
                <option value="1">MISSION</option>
                <option value="2">DEPLACEMENT</option>
            </select>
            <div id="categorie_field_container" style="display: none;">
                <input type="text" id="first_form_categorie" />
            </div>
        `;

        // Récupérer les éléments
        salarieSelect = document.getElementById('first_form_salarier');
        interneDiv = document.getElementById('Interne');
        externeDiv = document.getElementById('externe');
        matriculeInput = document.getElementById('first_form_matricule');
        typeMissionSelect = document.getElementById('first_form_typeMission');
        categorieFieldContainer = document.getElementById('categorie_field_container');
        categorieInput = document.getElementById('first_form_categorie');
        matriculeNomSelect = document.getElementById('first_form_matriculeNom');

        // Charger le script (simuler le comportement)
        require('../firstForm.js');
    });

    describe('Basculement Salarié Permanent/Temporaire', () => {
        test('Salarié PERMANENT affiche les champs Interne et masque Externe', () => {
            salarieSelect.value = 'PERMANENT';
            salarieSelect.dispatchEvent(new Event('change'));

            expect(interneDiv.style.display).toBe('block');
            expect(externeDiv.style.display).toBe('none');

            // Vérifier que les champs Interne sont activés
            const interneInputs = interneDiv.querySelectorAll('input, select');
            interneInputs.forEach(input => {
                expect(input.disabled).toBe(false);
            });

            // Vérifier que les champs Externe sont désactivés
            const externeInputs = externeDiv.querySelectorAll('input');
            externeInputs.forEach(input => {
                expect(input.disabled).toBe(true);
            });
        });

        test('Salarié TEMPORAIRE affiche les champs Externe et masque Interne', () => {
            salarieSelect.value = 'TEMPORAIRE';
            salarieSelect.dispatchEvent(new Event('change'));

            expect(interneDiv.style.display).toBe('none');
            expect(externeDiv.style.display).toBe('block');

            // Vérifier que les champs Externe sont activés
            const externeInputs = externeDiv.querySelectorAll('input');
            externeInputs.forEach(input => {
                expect(input.disabled).toBe(false);
                expect(input.required).toBe(true);
            });

            // Vérifier que les champs Interne sont désactivés
            const interneInputs = interneDiv.querySelectorAll('input, select');
            interneInputs.forEach(input => {
                expect(input.disabled).toBe(true);
                expect(input.required).toBe(false);
            });
        });
    });

    describe('Affichage du champ Catégorie', () => {
        test('Type mission MISSION affiche le champ catégorie', () => {
            typeMissionSelect.selectedIndex = 0; // MISSION
            typeMissionSelect.dispatchEvent(new Event('change'));

            expect(categorieFieldContainer.style.display).toBe('block');
            expect(categorieInput.required).toBe(true);
        });

        test('Type mission autre masque le champ catégorie', () => {
            typeMissionSelect.selectedIndex = 1; // DEPLACEMENT
            typeMissionSelect.dispatchEvent(new Event('change'));

            expect(categorieFieldContainer.style.display).toBe('none');
            expect(categorieInput.required).toBe(false);
        });
    });

    describe('Mise à jour automatique du matricule', () => {
        test('Sélection d\'un employé met à jour le matricule', () => {
            matriculeNomSelect.selectedIndex = 1; // Jean Dupont
            matriculeNomSelect.dispatchEvent(new Event('change'));

            expect(matriculeInput.value).toBe('MAT001');
        });

        test('Sélection d\'un autre employé met à jour le matricule', () => {
            matriculeNomSelect.selectedIndex = 2; // Marie Martin
            matriculeNomSelect.dispatchEvent(new Event('change'));

            expect(matriculeInput.value).toBe('MAT002');
        });

        test('Désélection efface le matricule', () => {
            matriculeNomSelect.selectedIndex = 1;
            matriculeNomSelect.dispatchEvent(new Event('change'));

            matriculeNomSelect.selectedIndex = 0; // Sélectionner vide
            matriculeNomSelect.dispatchEvent(new Event('change'));

            expect(matriculeInput.value).toBe('');
        });
    });
});
