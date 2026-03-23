import { Controller } from "@hotwired/stimulus";
import Swal from "sweetalert2";
import { validateFormFields } from "../../js/utils/form-validation";
import { validateSpecificForm } from "../../js/utils/form-specific-validation";

/**
 * Contrôleur Stimulus pour gérer la confirmation de soumission de formulaire
 * avec validation et messages SweetAlert2
 */
export default class extends Controller {
    static values = {
        confirmationMessage: { type: String, default: 'Êtes-vous sûr de vouloir soumettre ce formulaire ?' },
        warningMessage: { type: String, default: 'Veuillez ne pas fermer l\'onglet durant le traitement.' },
        confirmationText: { type: String, default: 'Cette action va enregistrer les données.' }
    };

    static targets = ['submitButton'];

    declare confirmationMessageValue: string;
    declare warningMessageValue: string;
    declare confirmationTextValue: string;
    declare submitButtonTarget: HTMLButtonElement;
    declare hasSubmitButtonTarget: boolean;

    private isSubmitting = false;

    /**
     * Gère la confirmation avant soumission du formulaire
     */
    async confirm(event: Event): Promise<void> {
        event.preventDefault();
        event.stopPropagation();

        // Éviter les soumissions multiples
        if (this.isSubmitting) {
            return;
        }

        const form = this.element.closest('form') as HTMLFormElement;
        if (!form) {
            console.error('Formulaire non trouvé');
            return;
        }

        // Validation générale des champs obligatoires
        const generalValidation = validateFormFields(form);
        if (!generalValidation.isValid) {
            await Swal.fire({
                title: 'Champs obligatoires manquants',
                html: generalValidation.errors.join('<br>'),
                icon: 'error',
                confirmButtonColor: '#fbbb01'
            });
            return;
        }

        // Validation spécifique au formulaire
        try {
            const formId = form.id || form.dataset.formId || '';
            const specificValidation = await validateSpecificForm(form, formId);

            if (!specificValidation.isValid) {
                await Swal.fire({
                    title: specificValidation.title || 'Erreur de validation',
                    html: specificValidation.message,
                    icon: 'error',
                    confirmButtonColor: '#fbbb01'
                });
                return;
            }
        } catch (error) {
            console.error('Erreur lors de la validation spécifique:', error);
            // Continuer sans validation spécifique
        }

        // Afficher le dialogue de confirmation
        const isConfirmed = await this.showConfirmationDialog();
        if (!isConfirmed) {
            return;
        }

        // Afficher l'avertissement
        await this.showWarningDialog();

        // Soumettre le formulaire
        this.submitForm(form);
    }

    /**
     * Affiche le dialogue de confirmation
     */
    private async showConfirmationDialog(): Promise<boolean> {
        const result = await Swal.fire({
            title: this.confirmationMessageValue,
            text: this.confirmationTextValue,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fbbb01',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OUI',
            cancelButtonText: 'Annuler'
        });

        return result.isConfirmed;
    }

    /**
     * Affiche le dialogue d'avertissement
     */
    private async showWarningDialog(): Promise<void> {
        await Swal.fire({
            title: 'Fait Attention!',
            text: this.warningMessageValue,
            icon: 'warning',
            confirmButtonColor: '#fbbb01',
            confirmButtonText: 'OK'
        });
    }

    /**
     * Soumet le formulaire avec overlay de chargement
     */
    private submitForm(form: HTMLFormElement): void {
        this.isSubmitting = true;

        // Afficher l'overlay de chargement si disponible
        const overlay = document.getElementById('loading-overlays');

        setTimeout(() => {
            if (overlay) {
                overlay.style.display = 'flex';
            }

            // Désactiver le bouton de soumission
            if (this.hasSubmitButtonTarget) {
                this.submitButtonTarget.disabled = true;
            }

            try {
                // Utiliser requestSubmit() au lieu de submit() pour préserver le token CSRF
                // et déclencher les événements de validation du formulaire
                form.requestSubmit();
            } catch (error) {
                console.error('Erreur lors de la soumission du formulaire:', error);

                // Réactiver en cas d'erreur
                if (overlay) {
                    overlay.style.display = 'none';
                }
                if (this.hasSubmitButtonTarget) {
                    this.submitButtonTarget.disabled = false;
                }
                this.isSubmitting = false;
            }
        }, 100);
    }
}
