import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["headerTitre"];
    declare readonly headerTitreTarget: HTMLElement;
    declare readonly hasHeaderTitreTarget: boolean;

    connect() {
        console.log('ListStickyController connected');
        this.updateStickyOffset();
        window.addEventListener('resize', this.updateStickyOffset.bind(this));

        // Gérer le cas où le DOM change (ex: messages flash qui disparaissent)
        this.observer = new MutationObserver(this.updateStickyOffset.bind(this));
        this.observer.observe(document.body, { childList: true, subtree: true });
    }

    disconnect() {
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
        // Un petit délai pour s'assurer que le rendu est fini
        setTimeout(() => {
            const globalHeader = document.querySelector('#app-main-header') as HTMLElement;
            const globalHeaderHeight = globalHeader ? globalHeader.offsetHeight : 0;

            // Définir le début du titre de la liste juste après le header global
            (this.element as HTMLElement).style.setProperty('--header-main-offset', `${globalHeaderHeight}px`);

            if (this.hasHeaderTitreTarget) {
                const titleHeight = this.headerTitreTarget.offsetHeight;
                const totalOffset = globalHeaderHeight + titleHeight;
                (this.element as HTMLElement).style.setProperty('--table-header-offset', `${totalOffset}px`);
                // console.log('Sticky offsets updated:', { globalHeaderHeight, totalOffset });
            } else {
                // Si pas de zone de titre spécifique, le tableau se cale sous le header global
                (this.element as HTMLElement).style.setProperty('--table-header-offset', `${globalHeaderHeight}px`);
            }
        }, 50);
    }
}
