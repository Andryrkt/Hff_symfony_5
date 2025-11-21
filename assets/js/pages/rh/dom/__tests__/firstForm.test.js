/**
 * @jest-environment jsdom
 */

import { SalarieFieldManager, CategorieFieldManager, MatriculeManager, initFirstForm } from '../firstForm';

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
    });

    describe('Basculement Salarié Permanent/Temporaire', () => {
        test('Salarié PERMANENT affiche les champs Interne et masque Externe', () => {
            const elements = {
                salarieSelect,
                interneDiv,
                externeDiv
            };
            const manager = new SalarieFieldManager(elements);

            salarieSelect.value = 'PERMANENT';
            manager.toggle();

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
            const elements = {
                salarieSelect,
                interneDiv,
                externeDiv
            };
            const manager = new SalarieFieldManager(elements);

            salarieSelect.value = 'TEMPORAIRE';
            manager.toggle();

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
            const elements = {
                typeMissionSelect,
                categorieFieldContainer,
                categorieInput
            };
            const manager = new CategorieFieldManager(elements);

            typeMissionSelect.selectedIndex = 0; // MISSION
            manager.toggle();

            expect(categorieFieldContainer.style.display).toBe('block');
            expect(categorieInput.required).toBe(true);
        });

        test('Type mission autre masque le champ catégorie', () => {
            const elements = {
                typeMissionSelect,
                categorieFieldContainer,
                categorieInput
            };
            const manager = new CategorieFieldManager(elements);

            typeMissionSelect.selectedIndex = 1; // DEPLACEMENT
            manager.toggle();

            expect(categorieFieldContainer.style.display).toBe('none');
            expect(categorieInput.required).toBe(false);
        });
    });

    describe('Mise à jour automatique du matricule', () => {
        test('Sélection d\'un employé met à jour le matricule', () => {
            const elements = {
                matriculeNomSelect,
                matriculeInput
            };
            const manager = new MatriculeManager(elements);

            matriculeNomSelect.selectedIndex = 1; // Jean Dupont
            manager.update();

            expect(matriculeInput.value).toBe('MAT001');
        });

        test('Sélection d\'un autre employé met à jour le matricule', () => {
            const elements = {
                matriculeNomSelect,
                matriculeInput
            };
            const manager = new MatriculeManager(elements);

            matriculeNomSelect.selectedIndex = 2; // Marie Martin
            manager.update();

            expect(matriculeInput.value).toBe('MAT002');
        });

        test('Désélection efface le matricule', () => {
            const elements = {
                matriculeNomSelect,
                matriculeInput
            };
            const manager = new MatriculeManager(elements);

            matriculeNomSelect.selectedIndex = 1;
            manager.update();

            matriculeNomSelect.selectedIndex = 0; // Sélectionner vide
            manager.update();

            expect(matriculeInput.value).toBe('');
        });
    });

    describe('Initialisation complète', () => {
        test('initFirstForm initialise tous les gestionnaires', () => {
            const managers = initFirstForm();

            expect(managers).toHaveProperty('salarieManager');
            expect(managers).toHaveProperty('categorieManager');
            expect(managers).toHaveProperty('matriculeManager');
            expect(managers.salarieManager).toBeInstanceOf(SalarieFieldManager);
            expect(managers.categorieManager).toBeInstanceOf(CategorieFieldManager);
            expect(managers.matriculeManager).toBeInstanceOf(MatriculeManager);
        });
    });
});
