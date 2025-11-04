document.addEventListener('DOMContentLoaded', function() {
    // --- Elements for Salarie ---
    const salarieSelect = document.getElementById('first_form_salarier');
    const interneDiv = document.getElementById('Interne');
    const externeDiv = document.getElementById('externe');
    const matriculeInput = document.getElementById('first_form_matricule');
    const nomInput = document.getElementById('first_form_nom');
    const prenomInput = document.getElementById('first_form_prenom');
    const cinInput = document.getElementById('first_form_cin');

    // --- Elements for TypeMission/Categorie ---
    const typeMissionSelect = document.getElementById('first_form_typeMission');
    const categorieFieldContainer = document.getElementById('categorie_field_container');
    const categorieInput = document.getElementById('first_form_categorie');

    // --- Elements for MatriculeNom ---
    const matriculeNomSelect = document.getElementById('first_form_matriculeNom');

    // --- Logic for Salarie ---
    function toggleSalarieFields() {
        if (!salarieSelect) return;
        const isTemporaire = salarieSelect.value === 'TEMPORAIRE';

        if (interneDiv) interneDiv.style.display = isTemporaire ? 'none' : 'block';
        if (externeDiv) externeDiv.style.display = isTemporaire ? 'block' : 'none';

        if (matriculeInput) matriculeInput.required = !isTemporaire;
        if (nomInput) nomInput.required = isTemporaire;
        if (prenomInput) prenomInput.required = isTemporaire;
        if (cinInput) cinInput.required = isTemporaire;
    }

    // --- Logic for Categorie ---
    function toggleCategorieField() {
        if (!typeMissionSelect) return;
        const isMission = typeMissionSelect.options[typeMissionSelect.selectedIndex]?.text === 'MISSION';
        if (categorieFieldContainer) categorieFieldContainer.style.display = isMission ? 'block' : 'none';
        if (categorieInput) categorieInput.required = isMission;
    }

    // --- Logic for MatriculeNom ---
    function updateMatriculeFromMatriculeNom() {
        if (!matriculeNomSelect || !matriculeInput) return;
        const selectedOption = matriculeNomSelect.options[matriculeNomSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.matricule) {
            matriculeInput.value = selectedOption.dataset.matricule;
        } else {
            matriculeInput.value = ''; // Clear if no selection or no data-matricule
        }
    }

    // --- Initial calls and event listeners ---
    toggleSalarieFields();
    toggleCategorieField();
    updateMatriculeFromMatriculeNom(); // Initial call might be needed if a value is pre-selected

    if (salarieSelect) salarieSelect.addEventListener('change', toggleSalarieFields);
    if (typeMissionSelect) typeMissionSelect.addEventListener('change', toggleCategorieField);
    if (matriculeNomSelect) matriculeNomSelect.addEventListener('change', updateMatriculeFromMatriculeNom);
});
