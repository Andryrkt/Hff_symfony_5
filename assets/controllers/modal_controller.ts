import { Controller } from "@hotwired/stimulus";

/**
 * Ce contrôleur écoute les événements d'un modal Bootstrap pour charger son contenu dynamiquement.
 */
export default class extends Controller {
    static targets = ["modalTitle", "modalContent"];

    private modalTitleTarget!: HTMLElement;
    private modalContentTarget!: HTMLElement;

    connect() {
        // this.element est le modal lui-même, car data-controller est sur l'élément du modal.
        this.element.addEventListener('show.bs.modal', this.onModalShow);
        this.element.addEventListener('hidden.bs.modal', this.resetModalContent);
    }

    disconnect() {
        this.element.removeEventListener('show.bs.modal', this.onModalShow);
        this.element.removeEventListener('hidden.bs.modal', this.resetModalContent);
    }

    /**
     * Se déclenche juste avant que le modal ne soit affiché.
     * Récupère l'élément déclencheur pour savoir quel contenu charger.
     */
    private onModalShow = (event: any) => {
        const triggerElement = event.relatedTarget as HTMLElement;
        if (!triggerElement) {
            return;
        }

        const cardId = triggerElement.dataset.cardId;
        if (cardId) {
            this.loadCardContent(cardId);
        } else {
            console.error("L'élément déclencheur n'a pas d'attribut 'data-card-id'", triggerElement);
            this.modalContentTarget.innerHTML = this.errorHtml("Impossible d'identifier le contenu à charger.");
        }
    }

    /**
     * Récupère et affiche le contenu de la carte.
     */
    private async loadCardContent(cardId: string) {
        this.modalContentTarget.innerHTML = this.loadingHtml();

        try {
            const response = await fetch(`/api/home/card/${cardId}`);
            if (!response.ok) {
                throw new Error(`Erreur serveur (statut: ${response.status})`);
            }
            const data = await response.json();
            this.displayCardContent(data);
        } catch (error) {
            let errorMessage = "Une erreur inconnue est survenue.";
            if (error instanceof Error) {
                errorMessage = error.message;
            }
            console.error("Erreur lors du chargement du contenu du modal:", error);
            this.modalContentTarget.innerHTML = this.errorHtml(errorMessage);
        }
    }

    /**
     * Met à jour le DOM du modal avec les données reçues.
     */
    private displayCardContent(data: any) {
        this.modalTitleTarget.textContent = data.title;

        if (data.links && data.links.length > 0) {
            let linksHtml = '<div class="list-group list-group-flush">';
            data.links.forEach((link: { url: string, label: string }) => {
                linksHtml += `
                    <a href="${link.url}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span>${link.label}</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                `;
            });
            linksHtml += '</div>';
            this.modalContentTarget.innerHTML = linksHtml;
        } else {
            this.modalContentTarget.innerHTML = this.emptyLinksHtml();
        }
    }

    /**
     * Réinitialise le contenu du modal après sa fermeture.
     */
    private resetModalContent = () => {
        this.modalTitleTarget.textContent = 'Navigation';
        this.modalContentTarget.innerHTML = this.loadingHtml();
    }

    // --- Méthodes utilitaires pour le HTML ---

    private errorHtml(message: string): string {
        return `<div class="alert alert-danger m-3"><strong>Erreur :</strong> ${message}</div>`;
    }

    private loadingHtml(): string {
        return `
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Chargement des liens...</p>
            </div>
        `;
    }

    private emptyLinksHtml(): string {
        return `
            <div class="text-center text-muted p-4">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>Aucun lien disponible pour cette section.</p>
            </div>
        `;
    }
}