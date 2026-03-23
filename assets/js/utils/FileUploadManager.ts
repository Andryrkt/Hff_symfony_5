import Swal from 'sweetalert2';

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
    expectedFileNamePrefix?: string;
    expectedKeywords?: string[];
    onFileSelect?: (file: File) => void;
    onFilesSelect?: (files: File[]) => void;
    onFileRemove?: () => void;
    container?: HTMLElement;
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
        const root = options.container || document;
        
        this.fileInput = options.fileInput;
        this.fileNameElement = root.querySelector(`#file-name-${idSuffix}`) || document.getElementById(`file-name-${idSuffix}`);
        this.fileSizeElement = root.querySelector(`#file-size-${idSuffix}`) || document.getElementById(`file-size-${idSuffix}`);
        this.fileSummaryElement = root.querySelector(`#file-summary-${idSuffix}`) || document.getElementById(`file-summary-${idSuffix}`);
        this.uploadBtn = root.querySelector(`#upload-btn-${idSuffix}`) || document.getElementById(`upload-btn-${idSuffix}`);
        this.removeBtn = root.querySelector(`#remove-btn-${idSuffix}`) || document.getElementById(`remove-btn-${idSuffix}`);
        this.dropzone = root.querySelector(`#dropzone-${idSuffix}`) || document.getElementById(`dropzone-${idSuffix}`);

        console.log(`[FileUploadManager] Initialisé pour ${idSuffix}`, {
            input: !!this.fileInput,
            removeBtn: !!this.removeBtn,
            dropzone: !!this.dropzone,
            root: options.container ? 'local' : 'global'
        });

        this.init();
    }

    private init(): void {
        if (this.uploadBtn) {
            this.uploadBtn.addEventListener('click', () => this.fileInput.click());
        }

        if (this.removeBtn) {
            this.removeBtn.addEventListener('click', (e) => {
                console.log(`[FileUploadManager] Clic sur bouton supprimer ${this.options.idSuffix}`);
                e.preventDefault();
                e.stopPropagation();
                this.clear(true);
            });
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

    private async processFiles(files: FileList): Promise<void> {
        const validFiles: File[] = [];
        const maxSizeBytes = (this.options.maxSizeMB || 5) * 1024 * 1024;

        const filesToProcess = this.options.multiple ? Array.from(files) : [files[0]];

        for (const file of filesToProcess) {
            if (!file) continue;

            // 1. Validation du type
            if (this.options.allowedTypes && !this.options.allowedTypes.includes(file.type)) {
                const ext = file.name.split('.').pop()?.toUpperCase();
                Swal.fire({
                    icon: 'error',
                    title: 'Type de fichier non autorisé',
                    text: `L'extension (${ext}) n'est pas acceptée. Veuillez utiliser : ${this.options.allowedTypes.join(', ')}`,
                    confirmButtonColor: '#ffc107',
                });
                return;
            }

            // 2. Validation de la taille
            if (file.size > maxSizeBytes) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fichier trop volumineux',
                    text: `Le fichier ${file.name} dépasse la limite autorisée de ${this.options.maxSizeMB} Mo.`,
                    confirmButtonColor: '#ffc107',
                });
                return;
            }

            // 3. Validation du format du NOM et du CONTENU (Si attendu)
            if (this.options.expectedFileNamePrefix || (this.options.expectedKeywords && this.options.expectedKeywords.length > 0)) {
                const isValid = await this.validateFileContent(file);
                if (!isValid) return;
            }

            validFiles.push(file);
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

    /**
     * Valide si le nom du fichier et son contenu correspond aux attentes de manière générique
     */
    private async validateFileContent(file: File): Promise<boolean> {
        const fileName = file.name.toUpperCase();

        // 1. Validation du PRÉFIXE du nom de fichier
        if (this.options.expectedFileNamePrefix) {
            const prefix = this.options.expectedFileNamePrefix.toUpperCase();
            if (!fileName.startsWith(prefix)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Format du nom de fichier invalide',
                    text: `Le nom du fichier a été renommé ou ne correspond pas à un fichier attendu.\nFichier actuel : ${file.name}`,
                    confirmButtonColor: '#ffc107',
                });
                return false;
            }
        }

        // 2. Validation des MOTS-CLÉS dans le contenu (PDF)
        // if (file.type === 'application/pdf' && this.options.expectedKeywords && this.options.expectedKeywords.length > 0) {
        //     try {
        //         const content = await this.readPdfAsText(file);
        //         const missingInContent = this.options.expectedKeywords.filter(
        //             word => word && !content.includes(word.toUpperCase())
        //         );

        //         if (missingInContent.length > 0) {
        //             Swal.fire({
        //                 icon: 'warning',
        //                 title: 'Vérification de contenu échouée',
        //                 text: `Les informations suivantes n'ont pas été trouvées dans le document : ${missingInContent.join(', ')}.\nVeuillez vérifier le fichier choisi.`,
        //                 confirmButtonColor: '#ffc107',
        //             });
        //             return false;
        //         }
        //     } catch (e) {
        //         console.error("Erreur lecture PDF", e);
        //     }
        // }

        return true;
    }

    /**
     * Lit un PDF comme du texte brut pour une recherche rapide de chaînes
     */
    private readPdfAsText(file: File): Promise<string> {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve((reader.result as string).toUpperCase());
            reader.onerror = reject;
            reader.readAsText(file); // Lecture textuelle pour trouver les IDs non compressés
        });
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
        console.log(`[FileUploadManager] Suppression du fichier (callback: ${triggerCallback})`);
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
