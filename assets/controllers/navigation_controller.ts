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
        this.setupDropdowns();
        this.setupUserMenu();
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
     * Configure les dropdowns
     */
    private setupDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', this.handleDropdownClick.bind(this));
        });
    }

    /**
     * Configure le menu utilisateur
     */
    private setupUserMenu() {
        const userMenu = document.getElementById('userName');
        if (userMenu) {
            userMenu.addEventListener('click', this.handleUserMenuClick.bind(this));
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
     * Gère les clics sur les dropdowns
     */
    private handleDropdownClick(event: Event) {
        const dropdown = event.currentTarget as HTMLElement;
        const isExpanded = dropdown.getAttribute('aria-expanded') === 'true';

        // Fermer tous les autres dropdowns
        document.querySelectorAll('.dropdown-toggle[aria-expanded="true"]').forEach(otherDropdown => {
            if (otherDropdown !== dropdown) {
                otherDropdown.setAttribute('aria-expanded', 'false');
            }
        });

        dropdown.setAttribute('aria-expanded', (!isExpanded).toString());
    }

    /**
     * Gère les clics sur le menu utilisateur
     */
    private handleUserMenuClick(event: Event) {
        console.log('User menu clicked');
        // Logique spécifique au menu utilisateur si nécessaire
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
     * Ferme tous les dropdowns ouverts
     */
    closeAllDropdowns() {
        document.querySelectorAll('.dropdown-toggle[aria-expanded="true"]').forEach(dropdown => {
            dropdown.setAttribute('aria-expanded', 'false');
        });
    }

    /**
     * Nettoie les ressources
     */
    private cleanup() {
        // Nettoyer les écouteurs d'événements si nécessaire
    }
}
