/**
 * FileUploadManager.ts
 * Utilitaire TypeScript pour la gestion des dropzones et prévisualisations de fichiers.
 */

export interface FileHandlerOptions {
    idSuffix: string;
    fileInput: HTMLInputElement;
    allowedTypes?: string[];
    maxSizeMB?: number;
    multiple?: boolean;
    onFileSelect?: (file: File) => void;
    onFilesSelect?: (files: File[]) => void;
    onFileRemove?: () => void;
}

export class FileUploadManager {
    private fileInput: HTMLInputElement;
    private fileNameElement: HTMLElement | null;
    private fileSizeElement: HTMLElement | null;
    private fileSummaryElement: HTMLElement | null;
    private uploadBtn: HTMLElement | null;
    private removeBtn: HTMLElement | null;
    private dropzone: HTMLElement | null;
    private options: FileHandlerOptions;
    private accumulatedFiles: File[] = [];

    constructor(options: FileHandlerOptions) {
        this.options = {
            allowedTypes: ['application/pdf'],
            maxSizeMB: 10,
            ...options
        };

        const { idSuffix } = this.options;
        this.fileInput = options.fileInput;
        this.fileNameElement = document.getElementById(`file-name-${idSuffix}`);
        this.fileSizeElement = document.getElementById(`file-size-${idSuffix}`);
        this.fileSummaryElement = document.getElementById(`file-summary-${idSuffix}`);
        this.uploadBtn = document.getElementById(`upload-btn-${idSuffix}`);
        this.removeBtn = document.getElementById(`remove-btn-${idSuffix}`);
        this.dropzone = document.getElementById(`dropzone-${idSuffix}`);

        this.init();
    }

    private init(): void {
        if (this.uploadBtn) {
            this.uploadBtn.addEventListener('click', () => this.fileInput.click());
        }

        if (this.removeBtn) {
            this.removeBtn.addEventListener('click', () => this.clear(true));
        }

        if (this.dropzone) {
            this.dropzone.addEventListener('dragover', (e) => this.handleDragOver(e));
            this.dropzone.addEventListener('dragleave', (e) => this.handleDragLeave(e));
            this.dropzone.addEventListener('drop', (e) => this.handleDrop(e));
        }

        this.fileInput.addEventListener('change', () => {
            if (this.fileInput.files && this.fileInput.files.length > 0) {
                this.processFiles(this.fileInput.files);
            }
        });
    }

    private handleDragOver(e: DragEvent): void {
        e.preventDefault();
        e.stopPropagation();
        if (this.dropzone) this.dropzone.style.backgroundColor = "#e2e6ea";
    }

    private handleDragLeave(e: DragEvent): void {
        e.preventDefault();
        e.stopPropagation();
        if (this.dropzone) this.dropzone.style.backgroundColor = "";
    }

    private handleDrop(e: DragEvent): void {
        e.preventDefault();
        e.stopPropagation();
        if (this.dropzone) this.dropzone.style.backgroundColor = "";

        const files = e.dataTransfer?.files;
        if (files && files.length > 0) {
            this.processFiles(files);
        }
    }

    private processFiles(files: FileList): void {
        const validFiles: File[] = [];
        const maxSizeBytes = (this.options.maxSizeMB || 5) * 1024 * 1024;
        let hasError = false;

        const filesToProcess = this.options.multiple ? Array.from(files) : [files[0]];

        for (const file of filesToProcess) {
            if (!file) continue;

            // Validation du type
            if (this.options.allowedTypes && !this.options.allowedTypes.includes(file.type)) {
                const ext = file.name.split('.').pop()?.toUpperCase();
                alert(`Type de fichier non autorisé (${ext}). Veuillez utiliser : ${this.options.allowedTypes.join(', ')}`);
                hasError = true;
                break;
            }

            // Validation de la taille
            if (file.size > maxSizeBytes) {
                alert(`Le fichier ${file.name} est trop volumineux (max ${this.options.maxSizeMB} Mo).`);
                hasError = true;
                break;
            }

            validFiles.push(file);
        }

        if (hasError) {
            // Restore previous files in input if there's an error, don't clear everything unless it's single
            if (!this.options.multiple) {
                this.clear();
            } else {
                const dt = new DataTransfer();
                for (const f of this.accumulatedFiles) {
                    dt.items.add(f);
                }
                this.fileInput.files = dt.files;
            }
            return;
        }

        if (validFiles.length > 0) {
            if (this.options.multiple) {
                this.accumulatedFiles.push(...validFiles);
            } else {
                this.accumulatedFiles = [validFiles[0]];
            }

            const dt = new DataTransfer();
            for (const f of this.accumulatedFiles) {
                dt.items.add(f);
            }
            this.fileInput.files = dt.files;

            this.updateUI(this.accumulatedFiles);
            if (this.options.multiple) {
                if (this.options.onFilesSelect) {
                    this.options.onFilesSelect(this.accumulatedFiles);
                }
            } else {
                if (this.options.onFileSelect) {
                    this.options.onFileSelect(this.accumulatedFiles[0]);
                }
            }
        }
    }


    private updateUI(files: File[]): void {
        if (this.options.multiple) {
            if (this.fileSummaryElement) {
                this.fileSummaryElement.innerHTML = `<strong>${files.length} fichier(s) sélectionné(s)</strong>`;
            }
        } else {
            const file = files[0];
            if (this.fileNameElement) {
                this.fileNameElement.innerHTML = `<strong>Fichier :</strong> ${file.name}`;
            }
            if (this.fileSizeElement) {
                this.fileSizeElement.innerHTML = `<strong>Taille :</strong> ${this.formatFileSize(file.size)}`;
            }
        }
        if (this.dropzone) {
            this.dropzone.style.borderColor = "#e0cc12ff";
            this.dropzone.style.backgroundColor = "rgba(40, 167, 69, 0.05)";
        }

        if (this.removeBtn) {
            this.removeBtn.classList.remove('d-none');
        }
    }

    public clear(triggerCallback: boolean = false): void {
        this.accumulatedFiles = [];
        this.fileInput.value = '';
        if (this.fileNameElement) this.fileNameElement.innerHTML = '';
        if (this.fileSizeElement) this.fileSizeElement.innerHTML = '';
        if (this.fileSummaryElement) this.fileSummaryElement.innerHTML = '';

        if (this.dropzone) {
            this.dropzone.style.borderColor = "#ccc";
            this.dropzone.style.backgroundColor = "";
        }

        if (this.removeBtn) {
            this.removeBtn.classList.add('d-none');
        }

        if (triggerCallback && this.options.onFileRemove) {
            this.options.onFileRemove();
        }
    }

    public getIdSuffix(): string {
        return this.options.idSuffix;
    }

    public removeFileByIndex(index: number, triggerCallback: boolean = false): void {
        if (index < 0 || index >= this.accumulatedFiles.length) return;

        this.accumulatedFiles.splice(index, 1);

        const dt = new DataTransfer();
        for (const f of this.accumulatedFiles) {
            dt.items.add(f);
        }

        this.fileInput.files = dt.files;

        if (this.accumulatedFiles.length === 0) {
            this.clear(triggerCallback);
        } else {
            // Reprocess the remaining files to trigger callbacks
            if (this.options.multiple) {
                if (this.options.onFilesSelect) {
                    this.options.onFilesSelect(this.accumulatedFiles);
                }
                if (this.fileSummaryElement) {
                    this.fileSummaryElement.innerHTML = `<strong>${this.accumulatedFiles.length} fichier(s) sélectionné(s)</strong>`;
                }
            }
        }
    }

    private formatFileSize(size: number): string {
        const units = ["B", "KB", "MB", "GB"];
        let unitIndex = 0;
        let adjustedSize = size;

        while (adjustedSize >= 1024 && unitIndex < units.length - 1) {
            adjustedSize /= 1024;
            unitIndex++;
        }

        return `${adjustedSize.toFixed(2)} ${units[unitIndex]}`;
    }
}
