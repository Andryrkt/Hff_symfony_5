import { Controller } from "@hotwired/stimulus";
/**
 * Ce contrôleur gère l'affichage et le contenu d'un modal Bootstrap,
 * avec support pour les menus imbriqués et les icônes.
 */
export default class extends Controller {
    static targets: string[];
    private modalTitleTarget;
    private modalContentTarget;
    connect(): void;
    disconnect(): void;
    private onModalShow;
    private loadCardContent;
    private displayCardContent;
    /**
     * Construit récursivement le HTML pour les liens, en ajoutant les icônes.
     */
    private buildLinksHtml;
    private resetModalContent;
    private errorHtml;
    private loadingHtml;
    private emptyLinksHtml;
}
//# sourceMappingURL=modal_controller.d.ts.map