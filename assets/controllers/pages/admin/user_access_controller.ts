// assets/controllers/user_access_controller.ts

import { Controller } from "@hotwired/stimulus";

interface UserAccessElement extends HTMLElement {
    userId?: string;
    accessId?: string;
}

export default class extends Controller {
    static values = { 
        userId: Number,
        accessId: Number 
    }

    declare readonly userIdValue: number;
    declare readonly accessIdValue: number;

    // Réessayer le chargement
    retry(): void {
        const frameId = `user_access_list_for_user_${this.userIdValue}`;
        const frame = this.getFrameById(frameId);
        
        if (!frame) {
            console.error(`Frame non trouvé: ${frameId}`);
            return;
        }

        this.reloadFrame(frame);
    }

    // Retirer un accès
    async removeAccess(event: Event): Promise<void> {
        const target = event.target as UserAccessElement;
        const accessId = target.dataset.userAccessId;
        const userId = this.userIdValue;

        if (!accessId || !userId) {
            console.error('ID utilisateur ou accès manquant');
            return;
        }

        if (!confirm('Êtes-vous sûr de vouloir retirer cet accès ?')) {
            return;
        }

        try {
            const response = await fetch(`/admin/user/${userId}/remove-access/${accessId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                }
            });

            if (response.ok) {
                this.reloadUserAccessFrame(userId);
            } else {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
        } catch (error) {
            console.error('Erreur lors du retrait de l\'accès:', error);
            this.showError('Erreur lors du retrait de l\'accès');
        }
    }

    // Gérer le toggle du collapse
    onToggleCollapse(event: Event): void {
        const target = event.target as HTMLElement;
        const userId = target.dataset.userAccessUserId;
        
        if (!userId) {
            console.warn('ID utilisateur manquant pour le toggle');
            return;
        }

        // Précharger le frame quand on ouvre le collapse
        const isExpanding = target.getAttribute('aria-expanded') === 'false';
        if (isExpanding) {
            this.preloadUserAccessFrame(parseInt(userId));
        }
    }

    // Méthodes utilitaires privées

    private getFrameById(frameId: string): HTMLFrameElement | null {
        const frame = document.getElementById(frameId);
        return frame as HTMLFrameElement | null;
    }

    private reloadFrame(frame: HTMLFrameElement): void {
        try {
            if (typeof (frame as any).reload === 'function') {
                (frame as any).reload();
            } else {
                // Fallback: recharger via src
                const currentSrc = frame.getAttribute('src');
                if (currentSrc) {
                    this.showLoadingState(frame);
                    frame.setAttribute('src', currentSrc);
                }
            }
        } catch (error) {
            console.error('Erreur lors du rechargement du frame:', error);
        }
    }

    private showLoadingState(frame: HTMLFrameElement): void {
        frame.innerHTML = `
            <div class="text-center text-muted py-3">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <small>Rechargement...</small>
            </div>
        `;
    }

    private reloadUserAccessFrame(userId: number): void {
        const frameId = `user_access_list_for_user_${userId}`;
        const frame = this.getFrameById(frameId);
        
        if (!frame) {
            console.warn(`Frame ${frameId} non trouvé pour le rechargement`);
            return;
        }

        this.reloadFrame(frame);
    }

    private preloadUserAccessFrame(userId: number): void {
        const frameId = `user_access_list_for_user_${userId}`;
        const frame = this.getFrameById(frameId);
        
        if (frame && !frame.hasAttribute('src')) {
            const src = `/admin/user/${userId}/access`;
            frame.setAttribute('src', src);
        }
    }

    private showError(message: string): void {
        const frame = this.getFrameById(`user_access_list_for_user_${this.userIdValue}`);
        
        if (frame) {
            frame.innerHTML = `
                <div class="alert alert-danger">
                    <p class="mb-2">${message}</p>
                    <button class="btn btn-sm btn-outline-secondary" 
                            data-action="click->user-access#retry">
                        Réessayer
                    </button>
                </div>
            `;
        } else {
            // Fallback: utiliser une alerte browser
            alert(message);
        }
    }
}