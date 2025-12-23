export function initPagination() {
    document.addEventListener('DOMContentLoaded', () => {
        const limitSelector = document.getElementById('limit-selector');
        if (limitSelector) {
            limitSelector.addEventListener('change', function (this: HTMLSelectElement) {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('limit', this.value);
                currentUrl.searchParams.set('page', '1'); // Reset to page 1 when changing limit
                window.location.href = currentUrl.toString();
            });
        }
    });
}