// assets/utils/turbo_error_handler.ts

interface TurboErrorDetail {
    response: Response;
    frame?: HTMLElement;
    fetchOptions?: RequestInit;
}

interface GlobalErrorOptions {
    duration?: number;
    dismissible?: boolean;
}

export class TurboErrorHandler {
    private errorContainer: HTMLElement | null;

    constructor(errorContainerId: string = 'global-errors') {
        this.errorContainer = document.getElementById(errorContainerId);
        this.init();
    }

    private init(): void {
        this.bindTurboEvents();
    }

    private bindTurboEvents(): void {
        // Gestion des frames Turbo manquants
        document.addEventListener('turbo:frame-missing', (event: Event) => {
            const customEvent = event as CustomEvent<TurboErrorDetail>;
            this.handleFrameMissing(customEvent.detail);
        });

        // Gestion des erreurs de requête Turbo
        document.addEventListener('turbo:request-error', (event: Event) => {
            const customEvent = event as CustomEvent<TurboErrorDetail>;
            this.handleRequestError(customEvent.detail);
        });

        // Gestion de la navigation
        document.addEventListener('turbo:before-fetch-response', (event: Event) => {
            const customEvent = event as CustomEvent<{ response: Response }>;
            this.handleFetchResponse(customEvent.detail.response);
        });
    }

    private handleFrameMissing(detail: TurboErrorDetail): void {
        const { response, frame } = detail;
        
        console.error('Frame Turbo manquant:', {
            frameId: frame?.id,
            status: response.status,
            url: response.url
        });
        
        // Empêcher le comportement par défaut
        event?.preventDefault();
        
        // Afficher un message d'erreur dans le frame
        if (frame?.id) {
            this.showFrameError(frame, `Erreur ${response.status} - Impossible de charger le contenu`);
        }
    }

    private handleRequestError(detail: TurboErrorDetail): void {
        const { response, fetchOptions } = detail;
        
        console.error('Erreur requête Turbo:', {
            url: (fetchOptions as any)?.url,
            status: response.status,
            method: fetchOptions?.method
        });
        
        if (response.status === 500) {
            this.showGlobalError('Une erreur serveur est survenue. Veuillez réessayer.');
        } else if (response.status === 404) {
            this.showGlobalError('La ressource demandée est introuvable.');
        }
    }

    private handleFetchResponse(response: Response): void {
        if (!response.ok) {
            console.warn('Réponse fetch non-OK:', response.status, response.statusText);
        }
    }

    // Afficher une erreur dans un frame spécifique
    public showFrameError(frame: HTMLElement, message: string): void {
        frame.innerHTML = `
            <div class="alert alert-danger">
                <h6>Erreur de chargement</h6>
                <p class="mb-2">${message}</p>
                <button class="btn btn-sm btn-outline-secondary" 
                        onclick="this.closest('turbo-frame')?.reload?.()">
                    Réessayer
                </button>
            </div>
        `;
    }

    // Afficher une erreur globale
    public showGlobalError(message: string, options: GlobalErrorOptions = {}): void {
        const { duration = 5000, dismissible = true } = options;

        if (!this.errorContainer) {
            console.warn('Conteneur d\'erreurs globales non trouvé');
            window.alert(message); // Fallback
            return;
        }

        const alert = document.createElement('div');
        alert.className = `alert alert-danger ${dismissible ? 'alert-dismissible fade show' : ''}`;
        alert.innerHTML = `
            ${message}
            ${dismissible ? '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' : ''}
        `;

        this.errorContainer.appendChild(alert);

        // Auto-dismiss si configuré
        if (duration > 0) {
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, duration);
        }
    }

    // Nettoyer toutes les erreurs
    public clearErrors(): void {
        if (this.errorContainer) {
            this.errorContainer.innerHTML = '';
        }
    }
}

// Utilisation
export const turboErrorHandler = new TurboErrorHandler();