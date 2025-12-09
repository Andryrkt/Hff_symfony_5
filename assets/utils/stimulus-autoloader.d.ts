import { Application } from '@hotwired/stimulus';
export declare class StimulusAutoloader {
    private application;
    constructor(application: Application);
    /**
     * Charge les contrôleurs automatiquement avec lazy loading
     * - Les contrôleurs "core" sont toujours chargés
     * - Les contrôleurs "page" sont chargés seulement si présents dans le DOM
     */
    autoload(): Promise<void>;
    /**
     * Vérifie si un contrôleur est utilisé dans le DOM actuel
     */
    private isControllerNeeded;
    /**
     * Charge un contrôleur spécifique
     */
    private loadController;
}
//# sourceMappingURL=stimulus-autoloader.d.ts.map