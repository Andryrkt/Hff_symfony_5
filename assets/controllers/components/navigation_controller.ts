import { Controller } from "@hotwired/stimulus"

/**
 * Contrôleur pour la navigation principale
 */
export default class extends Controller {
    static targets = ["navbar", "sidebar", "chronometer"]
    static values = {
        sessionTimeout: Number,
        baseUrl: String
    }

    private chronometer: any = null;
    private sessionManager: any = null;

    connect() {
        console.log("Navigation controller connected");
        this.initializeNavigation();
    }

    disconnect() {
        console.log("Navigation controller disconnected");
        this.cleanup();
    }

    /**
     * Initialise la navigation
     */
    private initializeNavigation() {
        this.setupSidebarToggle();
    }

    /**
     * Configure le toggle de la sidebar
     */
    private setupSidebarToggle() {
        const sidebarToggle = document.querySelector('[data-bs-toggle="collapse"]');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', this.handleSidebarToggle.bind(this));
        }
    }

    /**
     * Gère le toggle de la sidebar
     */
    private handleSidebarToggle(event: Event) {
        const target = event.currentTarget as HTMLElement;
        const targetId = target.getAttribute('data-bs-target');

        if (targetId) {
            const collapseElement = document.querySelector(targetId);
            if (collapseElement) {
                const isExpanded = target.getAttribute('aria-expanded') === 'true';
                target.setAttribute('aria-expanded', (!isExpanded).toString());
            }
        }
    }

    /**
     * Affiche/masque la sidebar
     */
    toggleSidebar() {
        const sidebar = document.querySelector('.flex-shrink-0');
        if (sidebar) {
            sidebar.classList.toggle('d-none');
        }
    }

    /**
     * Nettoie les ressources
     */
    private cleanup() {
        // Nettoyer les écouteurs d'événements si nécessaire
    }
}
