/**
 * Gestionnaire de session utilisateur
 */
export class SessionManager {
    private timeout: number | null = null;
    private chronometer: any = null;

    constructor() {
        // Le chronomètre sera initialisé séparément
        this.chronometer = null;
    }

    /**
     * Initialise la gestion de session
     */
    public init(): void {
        this.setupEventListeners();
        this.resetTimeout();
    }

    /**
     * Configure les écouteurs d'événements pour détecter l'activité utilisateur
     */
    private setupEventListeners(): void {
        const events = [
            'load',
            'mousemove',
            'keypress',
            'touchstart',
            'click',
            'scroll',
        ];

        events.forEach((event) => {
            window.addEventListener(event, () => {
                this.resetTimeout();
            });
        });

        // Surveiller les changements dans localStorage pour synchroniser les onglets
        window.addEventListener('storage', (event) => {
            if (event.key === 'session-active') {
                this.resetTimeout();
            }
        });
    }

    /**
     * Réinitialise le timeout et le chronomètre
     */
    private resetTimeout(): void {
        if (this.timeout) {
            clearTimeout(this.timeout);
        }

        // Réinitialiser le chronomètre si disponible
        if (this.chronometer) {
            this.chronometer.reset();
        }

        // Mettre à jour l'état dans localStorage
        localStorage.setItem('session-active', Date.now().toString());

        // Définir un nouveau timeout pour la déconnexion
        this.timeout = window.setTimeout(() => {
            window.location.href = '/logout';
        }, 900000); // 15 minutes
    }

    /**
     * Vérifie si la session est active
     */
    public isSessionActive(): boolean {
        const lastActivity = localStorage.getItem('session-active');
        if (!lastActivity) {
            return false;
        }

        const timeSinceLastActivity = Date.now() - parseInt(lastActivity);
        return timeSinceLastActivity < 900000; // 15 minutes
    }

    /**
     * Force la déconnexion
     */
    public forceLogout(): void {
        if (this.timeout) {
            clearTimeout(this.timeout);
        }
        localStorage.removeItem('session-active');
        window.location.href = '/logout';
    }
}
