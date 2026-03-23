import { Controller } from "@hotwired/stimulus";
import type PdfPreviewController from "../../../../common/pdf-preview_controller";

/**
 * Contrôleur spécifique à la page de soumission d'Ordres de Réparation.
 *
 * Responsabilités :
 *  - Déclarer les titres des pièces jointes (logique métier)
 *  - Écouter les events émis par les dropzone_controllers enfants
 *  - Déléguer l'affichage PDF au contrôleur commun `common--pdf-preview`
 *
 * Ce contrôleur NE gère plus directement les FileUploadManager.
 * C'est le rôle du `common--dropzone_controller` dans chaque dropzone.
 */
export default class extends Controller {
    static outlets = ["common--pdf-preview"];

    declare readonly "commonPdfPreviewOutlet": PdfPreviewController;
    declare readonly "hasCommonPdfPreviewOutlet": boolean;

    /**
     * Titres affichés dans les onglets PDF.
     * Clé = idSuffix du dropzone correspondant.
     * Configurable via data-value si besoin d'aller plus loin.
     */
    private readonly fileTitles: Record<string, string> = {
        '1': 'OR à valider *',
        '2': 'Devis',
        '3': 'BC ou autre document',
        '4': 'Autres documents',
    };

    connect() {
        console.log("✅ SoumissionOrsController connecté");
        console.log("🔗 Outlet PDF Preview présent ?", this.hasCommonPdfPreviewOutlet);
    }

    // ─── Handlers des events émis par common--dropzone ───────────────────────

    /**
     * Déclenché quand un fichier unique est sélectionné dans un dropzone.
     * Event detail : { id: string, file: File }
     */
    onDropzoneFileSelected(event: CustomEvent<{ id: string; file: File }>) {
        const { id, file } = event.detail;
        console.log(`📩 Fichier reçu du dropzone ${id}:`, file.name);
        const title = this.fileTitles[id] ?? `Fichier ${id}`;

        if (this.hasCommonPdfPreviewOutlet) {
            this.commonPdfPreviewOutlet.addFile(id, title, file);
        } else {
            console.error("❌ Outlet 'common--pdf-preview' non trouvé !");
        }
    }

    /**
     * Déclenché quand plusieurs fichiers sont sélectionnés (mode multiple).
     * Event detail : { id: string, files: File[] }
     */
    onDropzoneFilesSelected(event: CustomEvent<{ id: string; files: File[] }>) {
        const { id, files } = event.detail;
        const titlePrefix = this.fileTitles[id] ?? `Fichier ${id}`;

        if (this.hasCommonPdfPreviewOutlet) {
            this.commonPdfPreviewOutlet.addFiles(id, titlePrefix, files);
        }
    }

    /**
     * Déclenché quand un fichier est supprimé depuis le dropzone.
     * Event detail : { id: string }
     */
    onDropzoneFileRemoved(event: CustomEvent<{ id: string }>) {
        const { id } = event.detail;
        // id '4' est le seul en mode multiple dans ce formulaire
        const isMultiple = id === '4';

        if (this.hasCommonPdfPreviewOutlet) {
            this.commonPdfPreviewOutlet.removeFile(id, isMultiple);
        }
    }
}
