import { Application } from "@hotwired/stimulus";
import "bootstrap";
import "@fortawesome/fontawesome-free/css/all.min.css";
import "@fortawesome/fontawesome-free/js/all.min.js";
import "./styles/app.scss";
import "select2";
import "select2/dist/css/select2.css";
import "select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css";
import './styles/home.css';
/**
 * Classe principale de l'application
 */
declare class App {
    application: Application;
    constructor();
    /**
     * Initialise l'application
     */
    private init;
    /**
     * Charge les contrôleurs Stimulus
     */
    private loadStimulusControllers;
    /**
     * Initialise les gestionnaires
     */
    private initManagers;
    /**
     * Lie les événements globaux
     */
    private bindEvents;
    private onDomReady;
    private onTurboLoad;
    private initSelect2;
    /**
     * Instance singleton
     */
    static getInstance(): App;
}
declare global {
    interface Window {
        appInstance: any;
        $: any;
        Turbo: any;
    }
}
declare const app: App;
export default app;
//# sourceMappingURL=app.d.ts.map