import { Controller } from "@hotwired/stimulus";
/**
 * Contrôleur pour la navigation principale
 */
export default class extends Controller {
    static targets: string[];
    static values: {
        sessionTimeout: NumberConstructor;
        baseUrl: StringConstructor;
    };
    private chronometer;
    private sessionManager;
    connect(): void;
    disconnect(): void;
    /**
     * Initialise la navigation
     */
    private initializeNavigation;
    /**
     * Configure le toggle de la sidebar
     */
    private setupSidebarToggle;
    /**
     * Gère le toggle de la sidebar
     */
    private handleSidebarToggle;
    /**
     * Affiche/masque la sidebar
     */
    toggleSidebar(): void;
    /**
     * Nettoie les ressources
     */
    private cleanup;
}
//# sourceMappingURL=navigation_controller.d.ts.map