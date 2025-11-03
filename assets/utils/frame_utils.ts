// assets/utils/frame_utils.ts

export interface FrameAction {
    (frame: HTMLFrameElement): boolean | void;
}

export class FrameManager {
    /**
     * Exécute une action sur un frame en toute sécurité
     */
    static safeFrameAction(frameId: string, action: FrameAction): boolean {
        const frame = FrameManager.getFrameById(frameId);
        
        if (!frame) {
            console.warn(`Frame ${frameId} non trouvé`);
            return false;
        }
        
        try {
            const result = action(frame);
            return result !== false;
        } catch (error) {
            console.error(`Erreur lors de l'action sur le frame ${frameId}:`, error);
            return false;
        }
    }

    /**
     * Récupère un frame par son ID avec typage correct
     */
    static getFrameById(frameId: string): HTMLFrameElement | null {
        const element = document.getElementById(frameId);
        return element as HTMLFrameElement | null;
    }

    /**
     * Recharge un frame utilisateur
     */
    static reloadUserAccessFrame(userId: number): boolean {
        return this.safeFrameAction(
            `user_access_list_for_user_${userId}`, 
            (frame) => {
                if (typeof (frame as any).reload === 'function') {
                    (frame as any).reload();
                    return true;
                }
                return false;
            }
        );
    }

    /**
     * Vérifie si un frame existe
     */
    static frameExists(frameId: string): boolean {
        return this.getFrameById(frameId) !== null;
    }

    /**
     * Met à jour le contenu d'un frame de manière sécurisée
     */
    static updateFrameContent(frameId: string, content: string): boolean {
        return this.safeFrameAction(frameId, (frame) => {
            frame.innerHTML = content;
            return true;
        });
    }
}