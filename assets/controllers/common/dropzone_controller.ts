import { Controller } from "@hotwired/stimulus";
import { FileUploadManager } from "../../js/utils/FileUploadManager";

/**
 * Common dropzone controller that initializes a standard FileUploadManager
 * Can be attached to any dropzone html fragment
 */
export default class extends Controller {
    static values = {
        idSuffix: String,
        fileType: { type: String, default: "application/pdf" },
        multiple: { type: Boolean, default: false },
        maxSizeMb: { type: Number, default: 10 }
    }

    declare readonly idSuffixValue: string;
    declare readonly fileTypeValue: string;
    declare readonly multipleValue: boolean;
    declare readonly maxSizeMbValue: number;

    private manager: FileUploadManager;

    connect() {
        const input = (document.querySelector(`input[id$="_${this.idSuffixValue}"]`) ||
            document.querySelector(`input[id$="_pieceJoint0${this.idSuffixValue}"]`)) as HTMLInputElement;

        if (input) {
            this.manager = new FileUploadManager({
                idSuffix: this.idSuffixValue,
                fileInput: input,
                allowedTypes: [this.fileTypeValue],
                multiple: this.multipleValue,
                maxSizeMB: this.maxSizeMbValue,
                onFileSelect: (file) => {
                    this.dispatch('fileSelected', { detail: { id: this.idSuffixValue, file } });
                },
                onFilesSelect: (files) => {
                    this.dispatch('filesSelected', { detail: { id: this.idSuffixValue, files } });
                },
                onFileRemove: () => {
                    this.dispatch('fileRemoved', { detail: { id: this.idSuffixValue } });
                }
            });
        }
    }

    disconnect() {
        if (this.manager) {
            this.manager.clear();
        }
    }

    // Expose method to be called by outer controllers to manage files
    clearFiles(triggerCallback = false) {
        if (this.manager) this.manager.clear(triggerCallback);
    }

    removeFileByIndex(index: number, triggerCallback = false) {
        if (this.manager) this.manager.removeFileByIndex(index, triggerCallback);
    }
}
