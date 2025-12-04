import { Controller } from "@hotwired/stimulus";
import '../styles/login/login.scss';

export default class extends Controller {
    static targets = ["password", "toggleIcon", "toggleContainer"];

    declare passwordTarget: HTMLInputElement;
    declare toggleIconTarget: HTMLElement;
    declare toggleContainerTarget: HTMLElement;
    declare hasPasswordTarget: boolean;
    declare hasToggleIconTarget: boolean;
    declare hasToggleContainerTarget: boolean;

    connect() {
        console.log("🔐 Login controller connected");

        // Animation au chargement
        document.body.classList.add("loaded");

        // Nettoyage du localStorage
        this.clearLocalStorage();
    }

    /**
     * Bascule la visibilité du mot de passe
     */
    togglePassword() {
        if (!this.hasPasswordTarget || !this.hasToggleIconTarget) {
            console.error("Éléments introuvables pour la gestion du mot de passe");
            return;
        }

        const isVisible = this.passwordTarget.type === "text";
        this.passwordTarget.type = isVisible ? "password" : "text";

        this.toggleIconTarget.classList.toggle("fa-eye");
        this.toggleIconTarget.classList.toggle("fa-eye-slash");

        if (this.hasToggleContainerTarget) {
            this.toggleContainerTarget.dataset.bsOriginalTitle = isVisible
                ? "Afficher le mot de passe"
                : "Masquer le mot de passe";
        }
    }

    /**
     * Nettoie le localStorage
     */
    private clearLocalStorage() {
        try {
            localStorage.clear();
            console.log("localStorage nettoyé");
        } catch (error) {
            console.error("Erreur lors du nettoyage du localStorage:", error);
        }
    }
}
