export function initPagination() {
    const attachListener = () => {
        const limitSelector = document.getElementById('limit-selector') as HTMLSelectElement;
        if (limitSelector) {
            limitSelector.addEventListener('change', function (this: HTMLSelectElement) {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('limit', this.value);
                currentUrl.searchParams.set('page', '1'); // Reset to page 1 when changing limit

                this.disabled = true;
                const loadingIndicator = document.getElementById('limit-loading');
                if (loadingIndicator) {
                    loadingIndicator.classList.remove('d-none');
                }

                const ditFrame = document.getElementById('dit-list-frame');
                if (ditFrame) {
                    ditFrame.setAttribute('busy', '');
                }

                // Si Turbo est présent, on l'utilise pour une navigation fluide
                // @ts-ignore
                if (window.Turbo) {
                    // @ts-ignore
                    window.Turbo.visit(currentUrl.toString(), { action: "advance" });
                } else {
                    window.location.href = currentUrl.toString();
                }
            });
        }
    };

    // Initialisation au chargement classique et au chargement Turbo
    document.addEventListener('DOMContentLoaded', attachListener);
    document.addEventListener('turbo:load', attachListener);
}
