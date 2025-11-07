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

    // Gestion de l'affichage
    if (interneDiv) {
        interneDiv.style.display = isTemporaire ? 'none' : 'block';
        // Désactiver les champs requis quand masqués
        const interneInputs = interneDiv.querySelectorAll('input, select, textarea');
        interneInputs.forEach(input => {
            if (isTemporaire) {
                input.required = false;
                input.disabled = true; // Empêche la soumission
            } else {
                input.required = true; // ou restaurer l'état original
                input.disabled = false;
            }
        });
    }

    if (externeDiv) {
        externeDiv.style.display = isTemporaire ? 'block' : 'none';
        // Activer/désactiver les champs selon l'état
        const externeInputs = externeDiv.querySelectorAll('input, select, textarea');
        externeInputs.forEach(input => {
            if (isTemporaire) {
                input.required = true;
                input.disabled = false;
            } else {
                input.required = false;
                input.disabled = true; // Empêche la soumission
            }
        });
    }

    // Focus sur le premier champ visible
    setTimeout(() => {
        const firstVisibleInput = document.querySelector('input:not([disabled]):not([style*="display: none"])');
        if (firstVisibleInput) firstVisibleInput.focus();
    }, 100);
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