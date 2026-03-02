import { Controller } from "@hotwired/stimulus";
import { FetchManager } from "../../../../../js/utils/FetchManager";

export default class extends Controller {
    static targets = ["modalNumDit", "hiddenNumDit", "pollingContainer"];

    declare readonly modalNumDitTarget: HTMLElement;
    declare readonly hasModalNumDitTarget: boolean;
    declare readonly hiddenNumDitTarget: HTMLInputElement;
    declare readonly hasHiddenNumDitTarget: boolean;
    declare readonly pollingContainerTargets: HTMLElement[];

    private fetchManager: FetchManager;
    private pollingInterval: number | null = null;
    private readonly POLLING_DELAY = 10000; // 10 secondes

    connect() {
        console.log('DitListController connected');
        this.fetchManager = new FetchManager();
        this.startPolling();
    }

    disconnect() {
        this.stopPolling();
    }

    /**
     * Gère l'ouverture de la modal de soumission de document (via l'événement Bootstrap)
     * @param event 
     */
    showSoumissionModal(event: any) {
        console.log('showSoumissionModal triggered', event);
        // En mode événement show.bs.modal, relatedTarget est le bouton qui a ouvert la modal
        const button = event.relatedTarget;
        console.log('Related Target:', button);

        if (!button) {
            console.warn('No relatedTarget found for modal');
            return;
        }

        const numDit = button.getAttribute('data-numdit');
        console.log('NumDit found:', numDit);

        if (this.hasModalNumDitTarget) {
            this.modalNumDitTarget.textContent = numDit;
        }

        if (this.hasHiddenNumDitTarget) {
            this.hiddenNumDitTarget.value = numDit;
        }
    }

    /**
     * Démarre le polling global pour les numéros OR manquants
     */
    private startPolling() {
        if (this.pollingInterval) return;

        this.pollingInterval = window.setInterval(async () => {
            const pendingContainers = this.pollingContainerTargets.filter(
                c => c.getAttribute('data-has-or') === 'false'
            );

            if (pendingContainers.length === 0) {
                this.stopPolling();
                return;
            }

            const numeroDits = pendingContainers.map(c => c.getAttribute('data-num-dit'));

            try {
                const data = await this.fetchManager.post('hf/atelier/dit/async/check-numero-or-batch', {
                    numeroDits: numeroDits
                });

                if (data.results && Object.keys(data.results).length > 0) {
                    this.updateContainers(data.results);
                }
            } catch (error) {
                console.error('Erreur lors du batch polling du numéro OR:', error);
            }
        }, this.POLLING_DELAY);
    }

    /**
     * Arrête le polling
     */
    private stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }

    /**
     * Soumet le formulaire de recherche automatiquement
     */
    submitFilter() {
        const form = document.querySelector('#dit-search-form') as HTMLFormElement;
        if (form) {
            // Utiliser requestSubmit pour que Turbo capte l'événement
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        }
    }

    /**
     * Met à jour les conteneurs DOM avec les nouveaux numéros OR trouvés
     * @param results 
     */
    private updateContainers(results: Record<string, string>) {
        for (const [numDit, numeroOr] of Object.entries(results)) {
            const container = this.pollingContainerTargets.find(
                c => c.getAttribute('data-num-dit') === numDit
            );

            if (container) {
                container.setAttribute('data-has-or', 'true');
                container.innerHTML = `
                    <a href="#" data-bs-toggle="modal" data-bs-target="#listeCommande" data-id="${numeroOr}" class="numOr-link text-black" data-bs-toggle="tooltip" title="lister les commandes">
                        ${numeroOr}
                    </a>
                `;

                // @ts-ignore
                if (typeof showToast === 'function') {
                    // @ts-ignore
                    showToast('Succès', `Le numéro OR pour la DIT ${numDit} a été récupéré : ${numeroOr}`, 'success');
                }
            }
        }
    }
}
