/**
 * Gère l'affichage des champs selon le type de salarié
 */
export class SalarieFieldManager {
    constructor(elements) {
        this.salarieSelect = elements.salarieSelect;
        this.interneDiv = elements.interneDiv;
        this.externeDiv = elements.externeDiv;
    }

    /**
     * Bascule l'affichage entre champs Interne et Externe
     */
    toggle() {
        if (!this.salarieSelect) return;

        const isTemporaire = this.salarieSelect.value === 'TEMPORAIRE';
        this.updateFieldVisibility(this.interneDiv, !isTemporaire);
        this.updateFieldVisibility(this.externeDiv, isTemporaire);
        this.focusFirstVisibleInput();
    }

    /**
     * Met à jour la visibilité et l'état des champs d'un conteneur
     * @param {HTMLElement} container - Conteneur des champs
     * @param {boolean} isVisible - Si les champs doivent être visibles
     */
    updateFieldVisibility(container, isVisible) {
        if (!container) return;

        container.style.display = isVisible ? 'block' : 'none';
        container.setAttribute('aria-hidden', String(!isVisible));

        const inputs = container.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.required = isVisible;
            input.disabled = !isVisible;
            input.setAttribute('aria-required', String(isVisible));
            input.setAttribute('aria-disabled', String(!isVisible));
        });
    }

    /**
     * Met le focus sur le premier champ visible
     */
    focusFirstVisibleInput() {
        setTimeout(() => {
            const firstVisibleInput = document.querySelector(
                'input:not([disabled]):not([style*="display: none"])'
            );
            if (firstVisibleInput) firstVisibleInput.focus();
        }, 100);
    }
}

/**
 * Gère l'affichage du champ catégorie selon le type de mission
 */
export class CategorieFieldManager {
    constructor(elements) {
        this.typeMissionSelect = elements.typeMissionSelect;
        this.categorieFieldContainer = elements.categorieFieldContainer;
        this.categorieInput = elements.categorieInput;
    }

    /**
     * Affiche/masque le champ catégorie selon le type de mission
     */
    toggle() {
        if (!this.typeMissionSelect) return;

        const selectedOption = this.typeMissionSelect.options[
            this.typeMissionSelect.selectedIndex
        ];
        const isMission = selectedOption?.text === 'MISSION';

        if (this.categorieFieldContainer) {
            this.categorieFieldContainer.style.display = isMission ? 'block' : 'none';
            this.categorieFieldContainer.setAttribute('aria-hidden', String(!isMission));
        }

        if (this.categorieInput) {
            this.categorieInput.required = isMission;
            this.categorieInput.setAttribute('aria-required', String(isMission));
        }
    }
}

/**
 * Gère la mise à jour automatique du matricule
 */
export class MatriculeManager {
    constructor(elements) {
        this.matriculeNomSelect = elements.matriculeNomSelect;
        this.matriculeInput = elements.matriculeInput;
    }

    /**
     * Met à jour le champ matricule selon la sélection
     */
    update() {
        if (!this.matriculeNomSelect || !this.matriculeInput) return;

        const selectedOption = this.matriculeNomSelect.options[
            this.matriculeNomSelect.selectedIndex
        ];

        this.matriculeInput.value = selectedOption?.dataset.matricule || '';
    }
}

/**
 * Récupère tous les éléments DOM nécessaires
 * @returns {Object} Objet contenant tous les éléments
 */
function getFormElements() {
    return {
        salarieSelect: document.getElementById('first_form_salarier'),
        interneDiv: document.getElementById('Interne'),
        externeDiv: document.getElementById('externe'),
        typeMissionSelect: document.getElementById('first_form_typeMission'),
        categorieFieldContainer: document.getElementById('categorie_field_container'),
        categorieInput: document.getElementById('first_form_categorie'),
        matriculeNomSelect: document.getElementById('first_form_matriculeNom'),
        matriculeInput: document.getElementById('first_form_matricule'),
    };
}

/**
 * Initialise tous les gestionnaires du formulaire
 * @returns {Object} Gestionnaires initialisés
 */
export function initFirstForm() {
    const elements = getFormElements();

    // Clause de garde : ne rien faire si le formulaire n'est pas sur la page
    if (!elements.salarieSelect) {
        return;
    }

    const salarieManager = new SalarieFieldManager(elements);
    const categorieManager = new CategorieFieldManager(elements);
    const matriculeManager = new MatriculeManager(elements);

    // Appels initiaux pour définir l'état correct
    salarieManager.toggle();
    categorieManager.toggle();
    matriculeManager.update();

    // Ajout des event listeners
    if (elements.salarieSelect) {
        elements.salarieSelect.addEventListener('change', () => salarieManager.toggle());
    }

    if (elements.typeMissionSelect) {
        elements.typeMissionSelect.addEventListener('change', () => categorieManager.toggle());
    }

    if (elements.matriculeNomSelect) {
        elements.matriculeNomSelect.addEventListener('change', () => matriculeManager.update());
    }

    return { salarieManager, categorieManager, matriculeManager };
}
