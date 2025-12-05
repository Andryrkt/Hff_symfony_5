import { Application } from '@hotwired/stimulus';

export class StimulusAutoloader {
    private application: Application;

    constructor(application: Application) {
        this.application = application;
    }

    /**
     * Charge tous les contrôleurs automatiquement
     */
    async autoload(): Promise<void> {
        console.log('🔍 Chargement des contrôleurs Stimulus...');

        // Liste explicite de tous les contrôleurs
        const controllers = [
            // Contrôleurs génériques (UI components)
            { name: 'hello', import: () => import('@controllers/hello_controller') },
            { name: 'modal', import: () => import('@controllers/modal_controller') },
            { name: 'navigation', import: () => import('@controllers/navigation_controller') },
            { name: 'clickable', import: () => import('@controllers/inline_edit_controller') },

            // Contrôleurs de pages - Login
            { name: 'login', import: () => import('@controllers/pages/login/login_controller') },

            // Contrôleurs de pages - DOM (Ordres de Mission)
            { name: 'first-form', import: () => import('@controllers/pages/hf/rh/dom/first_form_controller') },
            { name: 'second-form', import: () => import('@controllers/pages/hf/rh/dom/second_form_controller') },
            { name: 'dom-liste', import: () => import('@controllers/pages/hf/rh/dom/dom_liste_controller') },

            // Contrôleurs de pages - Admin
            { name: 'user-roles', import: () => import('@controllers/pages/admin/user_roles_controller') },
            { name: 'user-access', import: () => import('@controllers/pages/admin/user_access_controller') },
        ];

        let loadedCount = 0;

        for (const controller of controllers) {
            const success = await this.loadController(controller.name, controller.import);
            if (success) loadedCount++;
        }

        console.log(`✅ ${loadedCount}/${controllers.length} contrôleurs chargés avec succès`);
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