import { Controller } from "@hotwired/stimulus";

/**
 * Ce contrôleur gère l'affichage et le contenu d'un modal Bootstrap,
 * avec support pour les menus imbriqués et les icônes.
 */
export default class extends Controller {
    static targets = ["modalTitle", "modalContent"];

    private modalTitleTarget!: HTMLElement;
    private modalContentTarget!: HTMLElement;

    connect() {
        this.element.addEventListener('show.bs.modal', this.onModalShow);
        this.element.addEventListener('hidden.bs.modal', this.resetModalContent);
    }

    disconnect() {
        this.element.removeEventListener('show.bs.modal', this.onModalShow);
        this.element.removeEventListener('hidden.bs.modal', this.resetModalContent);
    }

    private onModalShow = (event: any) => {
        const triggerElement = event.relatedTarget as HTMLElement;
        if (triggerElement?.dataset.cardId) {
            this.loadCardContent(triggerElement.dataset.cardId);
        } else {
            this.modalContentTarget.innerHTML = this.errorHtml("Impossible d'identifier le contenu à charger.");
        }
    }

    private async loadCardContent(cardId: string) {
        this.modalContentTarget.innerHTML = this.loadingHtml();
        try {
            const response = await fetch(`/api/home/card/${encodeURIComponent(cardId)}`);
            if (!response.ok) throw new Error(`Erreur serveur (statut: ${response.status})`);
            const data = await response.json();
            this.displayCardContent(data);
        } catch (error) {
            const message = error instanceof Error ? error.message : "Une erreur inconnue est survenue.";
            console.error("Erreur lors du chargement du contenu du modal:", error);
            this.modalContentTarget.innerHTML = this.errorHtml(message);
        }
    }

    private displayCardContent(data: any) {
        this.modalTitleTarget.textContent = data.title;
        if (data.links && data.links.length > 0) {
            this.modalContentTarget.innerHTML = this.buildLinksHtml(data.links, 'root');
        } else {
            this.modalContentTarget.innerHTML = this.emptyLinksHtml();
        }
    }

    /**
     * Construit récursivement le HTML pour les liens, en ajoutant les icônes.
     */
    private buildLinksHtml(links: any[], parentId: string): string {
        let html = '<div class="list-group list-group-flush">';

        links.forEach((link: { url: string, label: string, newTab: boolean, icon?: string, children?: any[] }, index) => {
            const hasChildren = link.children && link.children.length > 0;
            const elementId = `${parentId}-item-${index}`;
            const iconHtml = link.icon ? `<i class="${link.icon} fa-fw me-2"></i>` : '';

            if (hasChildren) {
                const collapseId = `collapse-${elementId}`;
                html += `
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#${collapseId}" role="button" aria-expanded="false" aria-controls="${collapseId}">
                        <span>${iconHtml}${link.label}</span>
                        <i class="fas fa-chevron-down fa-sm"></i>
                    </a>
                    <div class="collapse" id="${collapseId}">
                        <div class="ps-4 border-start ms-2">
                            ${this.buildLinksHtml(link.children!, elementId)}
                        </div>
                    </div>
                `;
            } else {
                const targetAttr = link.newTab ? 'target="_blank" rel="noopener noreferrer"' : '';
                html += `<a href="${link.url}" ${targetAttr} class="list-group-item list-group-item-action">${iconHtml}${link.label}</a>`;
            }
        });

        html += '</div>';
        return html;
    }

    private resetModalContent = () => {
        this.modalTitleTarget.textContent = 'Navigation';
        this.modalContentTarget.innerHTML = this.loadingHtml();
    }

    // --- Méthodes utilitaires pour le HTML ---

    private errorHtml = (message: string) => `<div class="alert alert-danger m-3"><strong>Erreur :</strong> ${message}</div>`;
    private loadingHtml = () => `<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div><p class="mt-2">Chargement des liens...</p></div>`;
    private emptyLinksHtml = () => `<div class="text-center text-muted p-4"><i class="fas fa-inbox fa-3x mb-3"></i><p>Aucun lien disponible pour cette section.</p></div>`;
}
