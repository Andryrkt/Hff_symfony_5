import { Application } from '@hotwired/stimulus';

export class StimulusAutoloader {
    private application: Application;

    constructor(application: Application) {
        this.application = application;
    }

    /**
     * Charge les contrôleurs automatiquement avec lazy loading
     * - Les contrôleurs "core" sont toujours chargés
     * - Les contrôleurs "page" sont chargés seulement si présents dans le DOM
     */
    async autoload(): Promise<void> {
        console.log('🔍 Chargement des contrôleurs Stimulus...');

        // Contrôleurs core - toujours chargés (UI réutilisable)
        const coreControllers = [
            { name: 'modal', import: () => import('@controllers/components/modal_controller') },
            { name: 'navigation', import: () => import('@controllers/components/navigation_controller') },
            { name: 'clickable', import: () => import('@controllers/components/inline_edit_controller') },
            { name: 'tom-select', import: () => import('@controllers/components/tom_select_controller') },
            { name: 'character-limiter', import: () => import('@controllers/components/character_limiter_controller') },
            { name: 'number-only', import: () => import('@controllers/components/number_only_controller') },
            { name: 'number-formatter', import: () => import('@controllers/components/number_formatter_controller') },
            { name: 'form-confirmation', import: () => import('@controllers/components/form_confirmation_controller') },
            { name: 'components--uppercase', import: () => import('@controllers/components/uppercase_controller') },
            { name: 'file-validation', import: () => import('@controllers/components/file_validation_controller') },
        ];

        // Contrôleurs de page - chargés à la demande (lazy loading)
        const pageControllers = [
            // Login
            { name: 'login', import: () => import('@controllers/pages/login/login_controller') },

            // DOM (Ordres de Mission)
            { name: 'first-form', import: () => import('@controllers/pages/hf/rh/dom/first_form_controller') },
            { name: 'second-form', import: () => import('@controllers/pages/hf/rh/dom/second_form_controller') },
            { name: 'dom-liste', import: () => import('@controllers/pages/hf/rh/dom/dom_liste_controller') },

            // Casier
            { name: 'casier-first-form', import: () => import('@controllers/pages/hf/materiel/casier/casier_first_form_controller') },
            { name: 'pages--hf--materiel--casier--liste', import: () => import('@controllers/pages/hf/materiel/casier/liste_controller') },


            // Admin
            { name: 'user-roles', import: () => import('@controllers/pages/admin/user_roles_controller') },
            { name: 'user-access', import: () => import('@controllers/pages/admin/user_access_controller') },

            // Handlers
            { name: 'badm-second-form', import: () => import('@/controllers/pages/hf/materiel/badm/badm_second_form_controller') },
            { name: 'badm-first-form', import: () => import('@controllers/pages/hf/materiel/badm/badm_first_form_controller') },

            // DIT
            { name: 'pages--hf--atelier--dit--dit-form', import: () => import('@controllers/pages/hf/atelier/dit/dit_form_controller') },
            { name: 'pages--hf--atelier--dit--dit-list', import: () => import('@controllers/pages/hf/atelier/dit/dit_list_controller') },
            { name: 'pages--hf--atelier--dit--soumission-ors', import: () => import('@controllers/pages/hf/atelier/dit/soumission-ors_controller') },
        ];

        let coreLoaded = 0;
        let pageLoaded = 0;

        // 1. Charger tous les contrôleurs core
        console.log('📦 Chargement des contrôleurs core...');
        for (const ctrl of coreControllers) {
            const success = await this.loadController(ctrl.name, ctrl.import);
            if (success) coreLoaded++;
        }

        // 2. Charger les contrôleurs de page seulement si présents dans le DOM
        console.log('📄 Chargement des contrôleurs de page (lazy)...');
        for (const ctrl of pageControllers) {
            if (this.isControllerNeeded(ctrl.name)) {
                const success = await this.loadController(ctrl.name, ctrl.import);
                if (success) pageLoaded++;
            }
        }

        console.log(`✅ Contrôleurs chargés: ${coreLoaded} core + ${pageLoaded} page`);
    }

    /**
     * Vérifie si un contrôleur est utilisé dans le DOM actuel
     */
    private isControllerNeeded(name: string): boolean {
        // Cherche data-controller="name" ou data-controller="... name ..."
        const selector = `[data-controller~="${name}"]`;
        return document.querySelector(selector) !== null;
    }

    /**
     * Charge un contrôleur spécifique
     */
    private async loadController(name: string, importFn: () => Promise<any>): Promise<boolean> {
        try {
            const module = await importFn();
            if (module?.default) {
                this.application.register(name, module.default);
                console.log(`  ✅ ${name}`);
                return true;
            } else {
                console.warn(`  ⚠️ Pas d'export default: ${name}`);
                return false;
            }
        } catch (error) {
            console.warn(`  ❌ Impossible de charger ${name}:`, error);
            return false;
        }
    }
}