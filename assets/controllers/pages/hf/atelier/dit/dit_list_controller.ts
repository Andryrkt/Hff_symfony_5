import { Controller } from "@hotwired/stimulus";
import { FetchManager } from "../../../../../js/utils/FetchManager";

export default class extends Controller {
    static targets = ["modalNumDit", "hiddenNumDit", "hiddenNumOr", "pollingContainer", "headerTitre"];

    declare readonly modalNumDitTarget: HTMLElement;
    declare readonly hasModalNumDitTarget: boolean;
    declare readonly hiddenNumDitTarget: HTMLInputElement;
    declare readonly hasHiddenNumDitTarget: boolean;
    declare readonly hiddenNumOrTarget: HTMLInputElement;
    declare readonly hasHiddenNumOrTarget: boolean;
    declare readonly pollingContainerTargets: HTMLElement[];
    declare readonly headerTitreTarget: HTMLElement;
    declare readonly hasHeaderTitreTarget: boolean;

    private fetchManager: FetchManager;
    private pollingInterval: number | null = null;
    private readonly POLLING_DELAY = 10000; // 10 secondes

    connect() {
        console.log('DitListController connected');
        this.fetchManager = new FetchManager();
        this.startPolling();

        // Initialiser le sticky header
        this.updateStickyOffset();
        window.addEventListener('resize', this.updateStickyOffset.bind(this));

        // Gérer le cas où le DOM change (ex: messages flash qui disparaissent)
        this.observer = new MutationObserver(this.updateStickyOffset.bind(this));
        this.observer.observe(document.body, { childList: true, subtree: true });
    }

    disconnect() {
        this.stopPolling();
        window.removeEventListener('resize', this.updateStickyOffset.bind(this));
        if (this.observer) {
            this.observer.disconnect();
        }
    }

    private observer: MutationObserver | null = null;

    /**
     * Met à jour dynamiquement la position sticky du header du tableau
     * en calculant la hauteur de tout ce qui se trouve au-dessus.
     */
    updateStickyOffset() {
        // Un petit délai pour s'assurer que le rendu est fini (surtout avec Turbo)
        setTimeout(() => {
            const globalHeader = document.querySelector('#app-main-header') as HTMLElement;
            const globalHeaderHeight = globalHeader ? globalHeader.offsetHeight : 0;

            // Définir le début du titre de la liste juste après le header global
            (this.element as HTMLElement).style.setProperty('--header-main-offset', `${globalHeaderHeight}px`);

            if (this.hasHeaderTitreTarget) {
                const titleHeight = this.headerTitreTarget.offsetHeight;
                const totalOffset = globalHeaderHeight + titleHeight;
                (this.element as HTMLElement).style.setProperty('--table-header-offset', `${totalOffset}px`);
                console.log('Sticky offsets updated:', { globalHeaderHeight, totalOffset });
            }
        }, 100);
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

        const numOr = button.getAttribute('data-numor');
        console.log('NumOr found:', numOr);

        if (this.hasHiddenNumOrTarget) {
            this.hiddenNumOrTarget.value = numOr;
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
