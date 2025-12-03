/*
 * Fichier JavaScript principal de l'application
 * Chargement automatique des contrôleurs Stimulus
 */

// Import Stimulus
import { Application } from "@hotwired/stimulus";

// Import de l'auto-loader
import { StimulusAutoloader } from "./utils/stimulus-autoloader";

// Import des styles et bibliothèques
import "bootstrap";
import "@fortawesome/fontawesome-free/css/all.min.css";
import "@fortawesome/fontawesome-free/js/all.min.js";
import "./styles/app.scss";
import "select2";
import "select2/dist/css/select2.css";
import "select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css";

// Import des utilitaires
import { ChronometerManager } from "./js/utils/chronometer";
import { SessionManager } from "./js/utils/session";
import { SubmenuManager } from "./js/utils/submenuManager";
import { ToastManager } from "./js/utils/toast";
import { CustomDropdown } from "./js/utils/customDropdown";
import { initFirstForm } from "./js/pages/rh/dom/firstForm.js";

// Import des styles supplémentaires
import './styles/home.css';

/**
 * Classe principale de l'application
 */
class App {
    public application: Application;

    constructor() {
        this.application = Application.start();
        this.init();
    }

    /**
     * Initialise l'application
     */
    private async init(): Promise<void> {
        console.log('🚀 Initialisation de l\'application...');

        try {
            // Charge automatiquement tous les contrôleurs Stimulus
            await this.loadStimulusControllers();

            // Initialise les autres fonctionnalités
            this.initManagers();
            this.bindEvents();

            console.log('🎯 Application prête !');

        } catch (error) {
            console.error('❌ Erreur lors de l\'initialisation:', error);
        }
    }

    /**
     * Charge les contrôleurs Stimulus
     */
    private async loadStimulusControllers(): Promise<void> {
        const autoloader = new StimulusAutoloader(this.application);
        await autoloader.autoload();
    }

    /**
     * Initialise les gestionnaires
     */
    private initManagers(): void {
        new ChronometerManager().init();
        new SessionManager().init();
        new ToastManager().init();
        new SubmenuManager().init();
    }

    /**
     * Lie les événements globaux
     */
    private bindEvents(): void {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onDomReady());
        } else {
            this.onDomReady();
        }

        if (typeof window.Turbo !== 'undefined') {
            document.addEventListener('turbo:load', () => this.onTurboLoad());
        }
    }

    private onDomReady(): void {
        // this.initSelect2();
        // this.initFirstForm();
        new CustomDropdown();
    }

    private onTurboLoad(): void {
        // this.initSelect2();
        // this.initFirstForm();
        new CustomDropdown();
    }

    private initSelect2(): void {
        const selectElements = document.querySelectorAll('select[data-select2]');
        selectElements.forEach((select: Element) => {
            const htmlSelect = select as HTMLSelectElement;
            if (htmlSelect && !htmlSelect.classList.contains('select2-hidden-accessible')) {
                try {
                    $(htmlSelect).select2({
                        theme: 'bootstrap-5',
                        width: '100%'
                    });
                } catch (error) {
                    console.error('Error initializing Select2:', error);
                }
            }
        });
    }

    private initFirstForm(): void {
        initFirstForm();
    }

    /**
     * Instance singleton
     */
    public static getInstance(): App {
        if (!window.appInstance) {
            window.appInstance = new App();
        }
        return window.appInstance;
    }
}

// Déclarations globales
declare global {
    interface Window {
        appInstance: any;
        $: any;
        Turbo: any;
    }
}

// Initialisation
const app = App.getInstance();

export default app;