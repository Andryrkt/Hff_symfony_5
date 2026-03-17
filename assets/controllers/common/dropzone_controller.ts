import { Controller } from "@hotwired/stimulus";
import { FileUploadManager } from "../../js/utils/FileUploadManager";

/**
 * Contrôleur commun pour les dropzones.
 * Initialise un FileUploadManager et émet des events Stimulus vers le contrôleur parent.
 *
 * Values disponibles (configurables via data-attributes HTML) :
 *   - idSuffix          : identifiant de la dropzone (obligatoire)
 *   - fileType          : MIME type accepté (défaut: "application/pdf")
 *   - multiple          : mode multi-fichiers (défaut: false)
 *   - maxSizeMb         : taille max en Mo (défaut: 10)
 *   - expectedPrefix    : préfixe attendu dans le nom de fichier (optionnel)
 *   - expectedKeywords  : mots-clés attendus, séparés par des virgules (optionnel)
 *
 * Events émis (écoutables par data-action sur l'élément parent) :
 *   - common--dropzone:fileSelected   → detail: { id, file }
 *   - common--dropzone:filesSelected  → detail: { id, files }
 *   - common--dropzone:fileRemoved    → detail: { id }
 */
export default class extends Controller {
    static values = {
        idSuffix:         String,
        fileType:         { type: String,  default: "application/pdf" },
        multiple:         { type: Boolean, default: false },
        maxSizeMb:        { type: Number,  default: 10 },
        expectedPrefix:   { type: String,  default: "" },
        expectedKeywords: { type: String,  default: "" },
    };

    declare readonly idSuffixValue:         string;
    declare readonly fileTypeValue:         string;
    declare readonly multipleValue:         boolean;
    declare readonly maxSizeMbValue:        number;
    declare readonly expectedPrefixValue:   string;
    declare readonly expectedKeywordsValue: string;

    private manager: FileUploadManager;

    connect() {
        const input = (
            this.element.querySelector(`input[id$="_${this.idSuffixValue}"]`) ||
            this.element.querySelector(`input[id$="_pieceJoint0${this.idSuffixValue}"]`) ||
            document.querySelector(`input[id$="_${this.idSuffixValue}"]`) ||
            document.querySelector(`input[id$="_pieceJoint0${this.idSuffixValue}"]`)
        ) as HTMLInputElement;

        if (!input) return;

        // Découpage des mots-clés séparés par virgule (ex: "DIT001,OR123")
        const keywords = this.expectedKeywordsValue
            ? this.expectedKeywordsValue.split(',').map(k => k.trim()).filter(Boolean)
            : [];

        this.manager = new FileUploadManager({
            idSuffix:                this.idSuffixValue,
            fileInput:               input,
            allowedTypes:            [this.fileTypeValue],
            multiple:                this.multipleValue,
            maxSizeMB:               this.maxSizeMbValue,
            expectedFileNamePrefix:  this.expectedPrefixValue || undefined,
            expectedKeywords:        keywords,
            onFileSelect: (file) => {
                this.dispatch('fileSelected', { detail: { id: this.idSuffixValue, file } });
            },
            onFilesSelect: (files) => {
                this.dispatch('filesSelected', { detail: { id: this.idSuffixValue, files } });
            },
            onFileRemove: () => {
                this.dispatch('fileRemoved', { detail: { id: this.idSuffixValue } });
            },
        });
    }

    disconnect() {
        if (this.manager) this.manager.clear();
    }

    // ─── API publique pour les contrôleurs parents ────────────────────────────

    clearFiles(triggerCallback = false) {
        if (this.manager) this.manager.clear(triggerCallback);
    }

    removeFileByIndex(index: number, triggerCallback = false) {
        if (this.manager) this.manager.removeFileByIndex(index, triggerCallback);
    }
}
