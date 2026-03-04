/**
 * FileUploadManager.ts
 * Utilitaire TypeScript pour la gestion des dropzones et prévisualisations de fichiers.
 */

export interface FileHandlerOptions {
    idSuffix: string;
    fileInput: HTMLInputElement;
    allowedTypes?: string[];
    maxSizeMB?: number;
    onFileSelect?: (file: File) => void;
    onFileRemove?: () => void;
}

export class FileUploadManager {
    private fileInput: HTMLInputElement;
    private fileNameElement: HTMLElement | null;
    private fileSizeElement: HTMLElement | null;
    private uploadBtn: HTMLElement | null;
    private removeBtn: HTMLElement | null;
    private dropzone: HTMLElement | null;
    private options: FileHandlerOptions;

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
            this.fileInput.files = files;
            this.processFiles(files);
        }
    }

    private processFiles(files: FileList): void {
        const file = files[0];
        if (!file) return;

        // Validation du type
        if (this.options.allowedTypes && !this.options.allowedTypes.includes(file.type)) {
            const ext = file.name.split('.').pop()?.toUpperCase();
            alert(`Type de fichier non autorisé (${ext}). Veuillez utiliser : ${this.options.allowedTypes.join(', ')}`);
            this.clear();
            return;
        }

        // Validation de la taille
        const maxSizeBytes = (this.options.maxSizeMB || 5) * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            alert(`Le fichier est trop volumineux (max ${this.options.maxSizeMB} Mo).`);
            this.clear();
            return;
        }

        this.updateUI(file);
        if (this.options.onFileSelect) {
            this.options.onFileSelect(file);
        }
    }

    private updateUI(file: File): void {
        if (this.fileNameElement) {
            this.fileNameElement.innerHTML = `<strong>Fichier :</strong> ${file.name}`;
        }
        if (this.fileSizeElement) {
            this.fileSizeElement.innerHTML = `<strong>Taille :</strong> ${this.formatFileSize(file.size)}`;
        }
        if (this.dropzone) {
            this.dropzone.style.borderColor = "#28a745";
            this.dropzone.style.backgroundColor = "rgba(40, 167, 69, 0.05)";
        }

        if (this.removeBtn) {
            this.removeBtn.classList.remove('d-none');
        }
    }

    public clear(triggerCallback: boolean = false): void {
        this.fileInput.value = '';
        if (this.fileNameElement) this.fileNameElement.innerHTML = '';
        if (this.fileSizeElement) this.fileSizeElement.innerHTML = '';

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
