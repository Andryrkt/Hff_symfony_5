import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        console.log('🔵 Clickable controller connected');
        this.element.addEventListener('click', this.handleClick.bind(this));
        (this.element as HTMLElement).style.cursor = 'pointer';
    }

    disconnect() {
        this.element.removeEventListener('click', this.handleClick.bind(this));
    }

    handleClick(event: Event) {
        const target = event.target as HTMLElement;
        
        console.log('🟡 Click detected on:', target);
        console.log('🟡 Clicked element tag:', target.tagName);
        console.log('🟡 Closest interactive:', target.closest('a, button, input, select'));

        // Ne pas interférer SEULEMENT si on clique directement sur un lien ou bouton
        // Mais autoriser le clic sur la ligne même si elle contient des liens
        const interactiveElement = target.closest('a, button, input, select');
        
        if (interactiveElement && interactiveElement !== this.element) {
            // Si on clique directement sur un lien dans le TD, laisser faire l'action normale
            if (interactiveElement.tagName === 'A') {
                console.log('🟠 Click on direct link, allowing default behavior');
                return;
            }
            console.log('🟠 Click on interactive element, ignoring');
            return;
        }

        console.log('🟢 Click on row, looking for edit link');
        
        // Trouver le lien edit
        const editLink = Array.from(this.element.querySelectorAll('a')).find(
            (a: HTMLAnchorElement) => a.href.includes('/edit')
        );

        if (editLink instanceof HTMLAnchorElement) {
            console.log('🚀 Found edit link:', editLink.href);
            
            // Forcer le chargement dans le turbo-frame correspondant
            const userId = this.extractUserIdFromRow();
            if (userId) {
                editLink.setAttribute('data-turbo-frame', `form-${userId}`);
            }
            
            // Déclencher le clic
            editLink.click();
        } else {
            console.log('❌ No edit link found');
        }
    }

    private extractUserIdFromRow(): string {
        // Extraire l'ID user du turbo-frame parent
        const turboFrame = this.element.closest('turbo-frame');
        if (turboFrame) {
            const id = turboFrame.id.replace('user-access-', '');
            console.log('Extracted user ID:', id);
            return id;
        }
        return '';
    }
}