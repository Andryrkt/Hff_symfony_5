/**
 * Gestionnaire du chronomètre de session
 */
export class ChronometerManager {
    private totalTime: number = 900; // 15 minutes en secondes
    private timeRemaining: number = this.totalTime;
    private timer: number | null = null;
    private chronoText: HTMLElement | null = null;
    private chronoProgress: HTMLElement | null = null;

    constructor() {
        this.chronoText = document.getElementById('chrono-text');
        this.chronoProgress = document.querySelector('.chrono-progress');
    }

    /**
     * Initialise le chronomètre
     */
    public init(): void {
        if (!this.chronoText || !this.chronoProgress) {
            console.warn('Éléments du chronomètre non trouvés');
            return;
        }

        this.updateChrono();
        this.startTimer();
    }

    /**
     * Met à jour l'affichage du chronomètre
     */
    private updateChrono(): void {
        this.timeRemaining--;

        // Calculer la progression (pourcentage inversé pour stroke-dashoffset)
        const circumference = 282.74;
        const progressPercentage = (this.timeRemaining / this.totalTime);
        const offset = circumference * (1 - progressPercentage);

        if (this.chronoProgress) {
            this.chronoProgress.style.strokeDashoffset = offset.toString();

            // Logique des couleurs
            this.chronoProgress.classList.remove('warning', 'danger');
            if (progressPercentage > 0.5) {
                // Vert par défaut
            } else if (progressPercentage > 0.2) {
                this.chronoProgress.classList.add('warning');
            } else {
                this.chronoProgress.classList.add('danger');
            }
        }

        // Mettre à jour le texte
        const minutes = Math.floor((this.timeRemaining % 3600) / 60);
        const seconds = this.timeRemaining % 60;

        if (this.chronoText) {
            this.chronoText.textContent = `${minutes.toString().padStart(2, '0')}:${seconds
                .toString()
                .padStart(2, '0')}`;
        }

        // Rediriger à la fin
        if (this.timeRemaining <= 0) {
            this.stopTimer();
            window.location.href = '/logout';
        } else if (this.timeRemaining <= 15) {
            this.showWarningToast();
        }
    }

    /**
     * Démarre le timer
     */
    private startTimer(): void {
        this.timer = window.setInterval(() => {
            this.updateChrono();
        }, 1000);
    }

    /**
     * Arrête le timer
     */
    public stopTimer(): void {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    /**
     * Réinitialise le chronomètre
     */
    public reset(): void {
        this.stopTimer();
        this.timeRemaining = this.totalTime;
        this.updateChrono();
        this.startTimer();
    }

    /**
     * Affiche un avertissement de session
     */
    private showWarningToast(): void {
        // Utiliser le gestionnaire de toast s'il est disponible
        if ((window as any).toastManager) {
            (window as any).toastManager.show('erreur', `Votre session va expirer dans ${this.timeRemaining} s.`);
        } else {
            console.warn(`Session va expirer dans ${this.timeRemaining} secondes`);
        }
    }
}
