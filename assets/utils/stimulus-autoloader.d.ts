import { Application } from '@hotwired/stimulus';
export declare class StimulusAutoloader {
    private application;
    constructor(application: Application);
    /**
     * Charge tous les contrôleurs automatiquement
     */
    autoload(): Promise<void>;
    /**
     * Charge un contrôleur spécifique
     */
    private loadController;
}
//# sourceMappingURL=stimulus-autoloader.d.ts.map