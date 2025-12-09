import { Controller } from '@hotwired/stimulus';

/**
 * Contrôleur Stimulus pour gérer le formulaire de création DOM (première étape)
 * Gère :
 * - Le basculement entre salarié permanent et temporaire
 * - L'affichage conditionnel du champ catégorie selon le type de mission
 * - La mise à jour automatique du matricule lors de la sélection d'un personnel
 */
export default class extends Controller {
    static targets = [
        'salarieSelect',
        'interneDiv',
        'externeDiv',
        'typeMissionSelect',
        'categorieFieldContainer',
        'categorieInput',
        'matriculeNomSelect',
        'matriculeInput'
    ];

    declare readonly salarieSelectTarget: HTMLSelectElement;
    declare readonly interneDivTarget: HTMLElement;
    declare readonly externeDivTarget: HTMLElement;
    declare readonly typeMissionSelectTarget: HTMLSelectElement;
    declare readonly categorieFieldContainerTarget: HTMLElement;
    declare readonly categorieInputTarget: HTMLInputElement;
    declare readonly matriculeNomSelectTarget: HTMLSelectElement;
    declare readonly matriculeInputTarget: HTMLInputElement;

    declare readonly hasSalarieSelectTarget: boolean;
    declare readonly hasTypeMissionSelectTarget: boolean;
    declare readonly hasMatriculeNomSelectTarget: boolean;

    /**
     * Initialisation du contrôleur
     */
    connect() {
        console.log('🎯 First Form Controller connecté');

        // Règle : le champ matricule est toujours en lecture seule.
        if (this.matriculeInputTarget) {
            this.matriculeInputTarget.readOnly = true;
            this.matriculeInputTarget.setAttribute('readonly', 'true');
        }

        // Définir l'état initial
        this.toggleSalarieFields();
        this.toggleCategorieField();
        this.updateMatricule();
    }

    /**
     * Gère le basculement entre champs Interne et Externe
     */
    toggleSalarieFields() {
        if (!this.hasSalarieSelectTarget) return;

        const isTemporaire = this.salarieSelectTarget.value === 'TEMPORAIRE';
        console.log('🔄 Toggle Salarié Fields - isTemporaire:', isTemporaire);

        this.updateFieldVisibility(this.interneDivTarget, !isTemporaire);
        this.updateFieldVisibility(this.externeDivTarget, isTemporaire);
        this.focusFirstVisibleInput();
    }

    /**
     * Affiche/masque le champ catégorie selon le type de mission
     */
    toggleCategorieField() {
        if (!this.hasTypeMissionSelectTarget) return;

        const selectedOption = this.typeMissionSelectTarget.options[
            this.typeMissionSelectTarget.selectedIndex
        ];
        const isMission = selectedOption?.text === 'MISSION';

        if (this.categorieFieldContainerTarget) {
            this.categorieFieldContainerTarget.style.display = isMission ? 'block' : 'none';
            this.categorieFieldContainerTarget.setAttribute('aria-hidden', String(!isMission));
        }

        if (this.categorieInputTarget) {
            this.categorieInputTarget.required = isMission;
            this.categorieInputTarget.setAttribute('aria-required', String(isMission));
        }
    }

    /**
     * Met à jour le champ matricule et gère l'état de validation personnalisé.
     */
    updateMatricule() {
        if (!this.hasMatriculeNomSelectTarget || !this.matriculeInputTarget) return;

        const selectedOption = this.matriculeNomSelectTarget.options[
            this.matriculeNomSelectTarget.selectedIndex
        ];

        this.matriculeInputTarget.value = selectedOption?.dataset.matricule || '';

        // Si une sélection est faite, on efface immédiatement tout message d'erreur personnalisé.
        if (this.matriculeNomSelectTarget.value) {
            this.matriculeNomSelectTarget.setCustomValidity('');
        }

        this.matriculeNomSelectTarget.dispatchEvent(new Event('blur'));
    }

    /**
     * Gère la validation manuelle du champ matriculeNom avant la soumission du formulaire.
     */
    validate(event: SubmitEvent) {
        const isInterneVisible = this.interneDivTarget.style.display !== 'none';

        // On ne valide que si la section est visible et si le champ est vide.
        if (isInterneVisible && this.matriculeNomSelectTarget.value === '') {
            // On empêche le formulaire de s'envoyer.
            event.preventDefault();

            // On crée et affiche notre propre message de validation.
            const errorMessage = "Veuillez sélectionner une personne dans la liste.";
            this.matriculeNomSelectTarget.setCustomValidity(errorMessage);
            this.matriculeNomSelectTarget.reportValidity();

            // On met le focus sur le champ pour aider l'utilisateur.
            const tomSelect = (this.matriculeNomSelectTarget as any).tomselect;
            if (tomSelect) {
                tomSelect.focus();
            }
        } else {
            // Si tout va bien, on s'assure qu'il n'y a pas de message d'erreur résiduel.
            this.matriculeNomSelectTarget.setCustomValidity('');
        }
    }

    /**
     * Met à jour la visibilité et l'état des champs d'un conteneur.
     */
    private updateFieldVisibility(container: HTMLElement, isVisible: boolean) {
        if (!container) return;

        container.style.display = isVisible ? 'block' : 'none';
        container.setAttribute('aria-hidden', String(!isVisible));

        const inputs = container.querySelectorAll('input, select, textarea');
        inputs.forEach((input: Element) => {
            const htmlInput = input as HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement;

            // Condition spéciale pour désactiver la validation native sur le champ problématique.
            if (htmlInput === this.matriculeNomSelectTarget) {
                htmlInput.required = false;
            } else {
                htmlInput.required = isVisible;
            }

            htmlInput.disabled = !isVisible;
            htmlInput.setAttribute('aria-required', String(isVisible));
            htmlInput.setAttribute('aria-disabled', String(!isVisible));

            // Gérer Tom Select si présent
            const tomSelectInstance = (htmlInput as any).tomselect;
            if (tomSelectInstance) {
                if (isVisible) {
                    tomSelectInstance.enable();
                } else {
                    tomSelectInstance.disable();
                }
            }
        });
    }

    /**
     * Met le focus sur le premier champ visible du conteneur actif
     */
    private focusFirstVisibleInput() {
        setTimeout(() => {
            // Chercher dans le conteneur visible (interne ou externe)
            const visibleContainer = this.interneDivTarget.style.display !== 'none'
                ? this.interneDivTarget
                : this.externeDivTarget;

            const firstVisibleInput = visibleContainer.querySelector(
                'input:not([disabled]):not([readonly]), select:not([disabled])'
            ) as HTMLInputElement | HTMLSelectElement;

            if (firstVisibleInput) firstVisibleInput.focus();
        }, 100);
    }
}
