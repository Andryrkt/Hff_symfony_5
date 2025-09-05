/**
 * Gestionnaire des notifications toast
 */
export class ToastManager {
    private container: HTMLElement | null = null;

    constructor() {
        this.createContainer();
    }

    /**
     * Initialise le gestionnaire de toast
     */
    public init(): void {
        // Rendre le gestionnaire globalement accessible
        (window as any).toastManager = this;
    }

    /**
     * Crée le conteneur pour les toasts
     */
    private createContainer(): void {
        this.container = document.createElement('div');
        this.container.id = 'toast-container';
        this.container.className = 'toast-container position-fixed top-0 end-0 p-3';
        this.container.style.zIndex = '9999';
        document.body.appendChild(this.container);
    }

    /**
     * Affiche une notification toast
     */
    public show(type: 'success' | 'error' | 'warning' | 'info' | 'erreur', message: string, duration: number = 5000): void {
        if (!this.container) {
            console.error('Conteneur de toast non trouvé');
            return;
        }

        // Normaliser le type
        const normalizedType = type === 'erreur' ? 'error' : type;

        const toastId = `toast-${Date.now()}`;
        const toastHtml = this.createToastHtml(toastId, normalizedType, message);

        this.container.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.getElementById(toastId);
        if (toastElement) {
            const toast = new (window as any).bootstrap.Toast(toastElement, {
                autohide: true,
                delay: duration
            });

            toast.show();

            // Supprimer l'élément du DOM après fermeture
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }
    }

    /**
     * Crée le HTML pour un toast
     */
    private createToastHtml(id: string, type: string, message: string): string {
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        const colors = {
            success: 'text-success',
            error: 'text-danger',
            warning: 'text-warning',
            info: 'text-info'
        };

        const icon = icons[type as keyof typeof icons] || icons.info;
        const color = colors[type as keyof typeof colors] || colors.info;

        return `
            <div id="${id}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="${icon} ${color} me-2"></i>
                    <strong class="me-auto">Notification</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
    }

    /**
     * Affiche un toast de succès
     */
    public success(message: string, duration?: number): void {
        this.show('success', message, duration);
    }

    /**
     * Affiche un toast d'erreur
     */
    public error(message: string, duration?: number): void {
        this.show('error', message, duration);
    }

    /**
     * Affiche un toast d'avertissement
     */
    public warning(message: string, duration?: number): void {
        this.show('warning', message, duration);
    }

    /**
     * Affiche un toast d'information
     */
    public info(message: string, duration?: number): void {
        this.show('info', message, duration);
    }
}
