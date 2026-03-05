import { Controller } from "@hotwired/stimulus";
import { FileUploadManager } from "../../../../../js/utils/FileUploadManager";

export default class extends Controller {
    static targets = ["pdfPreview", "placeholder", "navTabs", "tabContent"];

    declare readonly pdfPreviewTarget: HTMLElement;
    declare readonly hasPdfPreviewTarget: boolean;
    declare readonly placeholderTarget: HTMLElement;
    declare readonly hasPlaceholderTarget: boolean;
    declare readonly navTabsTarget: HTMLElement;
    declare readonly hasNavTabsTarget: boolean;
    declare readonly tabContentTarget: HTMLElement;
    declare readonly hasTabContentTarget: boolean;

    private uploadedFiles: Record<string, { title: string, dataUrl: string }> = {};

    private fileTitles: Record<string, string> = {
        '1': 'Document OR',
        '2': 'Devis',
        '3': 'BC ou autre',
        '4': 'Autre document'
    };

    private managers: FileUploadManager[] = [];

    connect() {
        console.log("SoumissionOrsController connected");
        this.initDropzones();
    }

    private initDropzones() {
        const ids = ['1', '2', '3', '4'];

        ids.forEach(id => {
            // Chercher l'input dans le contexte du controller (this.element) en priorité,
            // puis dans le document entier si pas trouvé
            const input = (
                this.element.querySelector(`input[id$="_pieceJoint0${id}"]`) ||
                document.querySelector(`input[id$="_pieceJoint0${id}"]`)
            ) as HTMLInputElement;

            if (input) {
                // Récupération des valeurs attendues depuis le formulaire pour la validation
                const numeroDitInput = document.querySelector('input[id$="_numeroDit"]') as HTMLInputElement;
                const numeroOrInput = document.querySelector('input[id$="_numeroOr"]') as HTMLInputElement;

                const manager = new FileUploadManager({
                    idSuffix: id,
                    fileInput: input,
                    allowedTypes: ['application/pdf'],
                    maxSizeMB: 10,
                    multiple: id === '4',
                    // Règle spécifique pour le document OR (id=1)
                    expectedFileNamePrefix: id === '1' ? `Ordre de réparation_${numeroOrInput?.value}` : undefined,
                    expectedKeywords: id === '1' ? [numeroDitInput?.value, numeroOrInput?.value] : [],
                    onFileSelect: (file) => {
                        this.handleFileSelect(id, file);
                    },
                    onFilesSelect: (files) => {
                        this.handleFilesSelect(id, files);
                    },
                    onFileRemove: () => {
                        this.handleFileRemove(id);
                    }
                });
                this.managers.push(manager);
            }
        });
    }

    private handleFileSelect(id: string, file: File) {
        const reader = new FileReader();
        reader.onload = (e) => {
            if (e.target?.result) {
                this.uploadedFiles[id] = {
                    title: this.fileTitles[id] || `Fichier ${id}`,
                    dataUrl: e.target.result as string
                };
                this.renderTabs();
            }
        };
        reader.readAsDataURL(file);
    }

    private handleFilesSelect(id: string, files: File[]) {
        // Nettoyer les anciens fichiers pour cet id s'il y en avait
        Object.keys(this.uploadedFiles).forEach(key => {
            if (key.startsWith(`${id}_`)) {
                delete this.uploadedFiles[key];
            }
        });

        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (e.target?.result) {
                    const fileKey = `${id}_${index}`;
                    this.uploadedFiles[fileKey] = {
                        title: `${this.fileTitles[id] || 'Fichier'} ${index + 1} (${file.name})`,
                        dataUrl: e.target.result as string
                    };

                    // On rend les onglets uniquement quand le dernier fichier a été lu
                    if (index === files.length - 1) {
                        this.renderTabs();
                    }
                }
            };
            reader.readAsDataURL(file);
        });
    }

    private handleFileRemove(id: string) {
        if (id === '4') {
            Object.keys(this.uploadedFiles).forEach(key => {
                if (key.startsWith(`${id}_`)) {
                    delete this.uploadedFiles[key];
                }
            });
        } else {
            delete this.uploadedFiles[id];
        }
        this.renderTabs();
    }

    private renderTabs() {
        const ids = Object.keys(this.uploadedFiles).sort();

        if (ids.length === 0) {
            this.hidePreview();
            return;
        }

        this.showPreview();

        if (!this.hasNavTabsTarget || !this.hasTabContentTarget) return;

        let activeId = ids[0];
        // If the currently active tab is still here, keep it active
        const currentActiveTab = this.navTabsTarget.querySelector('.nav-link.active');
        if (currentActiveTab) {
            const currentId = currentActiveTab.getAttribute('data-id');
            if (currentId && this.uploadedFiles[currentId]) {
                activeId = currentId;
            }
        }

        this.navTabsTarget.innerHTML = '';
        this.tabContentTarget.innerHTML = '';

        ids.forEach(id => {
            const data = this.uploadedFiles[id];
            const isActive = id === activeId;

            // Tab button
            const li = document.createElement('li');
            li.className = 'nav-item position-relative d-flex align-items-center';

            const a = document.createElement('a');
            a.className = `nav-link text-black border-0 rounded-top pe-4 ${isActive ? 'active text-primary fw-bold bg-white' : ''}`;
            a.style.cursor = 'pointer';
            a.textContent = data.title;
            a.setAttribute('data-id', id);
            a.setAttribute('data-action', 'click->pages--hf--atelier--dit--soumission-ors#switchTab');

            li.appendChild(a);

            // Close button
            const closeBtn = document.createElement('span');
            closeBtn.className = 'position-absolute top-50 end-0 translate-middle-y me-2 text-danger';
            closeBtn.style.cursor = 'pointer';
            closeBtn.style.fontSize = '1.1rem';
            closeBtn.style.zIndex = '10';
            closeBtn.innerHTML = '<i class="fas fa-trash"></i>';
            closeBtn.setAttribute('data-id', id);
            closeBtn.setAttribute('data-action', 'click->pages--hf--atelier--dit--soumission-ors#removePreviewTab');

            li.appendChild(closeBtn);

            this.navTabsTarget.appendChild(li);

            // Tab content
            const div = document.createElement('div');
            div.className = `tab-pane h-100 ${isActive ? 'active show' : 'd-none'}`;
            div.setAttribute('data-pane-id', id);

            const embed = document.createElement('embed');
            embed.src = data.dataUrl;
            embed.type = 'application/pdf';
            embed.style.width = '100%';
            embed.style.height = '100%';

            div.appendChild(embed);
            this.tabContentTarget.appendChild(div);
        });
    }

    switchTab(event: Event) {
        event.preventDefault();
        const clickedTab = event.currentTarget as HTMLElement;
        const targetId = clickedTab.getAttribute('data-id');

        if (!targetId || !this.hasNavTabsTarget || !this.hasTabContentTarget) return;

        // Reset active tabs
        this.navTabsTarget.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active', 'text-primary', 'bg-white', 'fw-bold');
            link.classList.add('text-white');
        });

        // Hide all panes
        this.tabContentTarget.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active', 'show');
            pane.classList.add('d-none');
        });

        // Set active tab
        clickedTab.classList.add('active', 'text-primary', 'bg-white', 'fw-bold');
        clickedTab.classList.remove('text-white');

        // Show target pane
        const targetPane = this.tabContentTarget.querySelector(`.tab-pane[data-pane-id="${targetId}"]`);
        if (targetPane) {
            targetPane.classList.remove('d-none');
            targetPane.classList.add('active', 'show');
        }
    }

    removePreviewTab(event: Event) {
        event.preventDefault();
        event.stopPropagation();

        const closeBtn = event.currentTarget as HTMLElement;
        const targetId = closeBtn.getAttribute('data-id');

        if (!targetId) return;

        // Trouver le contrôleur dropzone correspondant dans le DOM et appeler sa méthode pour virer le fichier
        let managerBaseId = targetId;
        let indexStr = null;

        if (targetId.includes('_')) {
            // Fichier issu d'une sélection multiple (ex: "4_0", "4_1")
            const parts = targetId.split('_');
            managerBaseId = parts[0];
            indexStr = parts[1];
        }

        const dropzoneControllerElement = document.getElementById(`dropzone-${managerBaseId}`);

        if (dropzoneControllerElement) {
            // Hotwire permet de récupérer une action du contrôleur si on connaît son nom
            // via application.getControllerForElementAndIdentifier mais en typescript c'est bloquant 
            // On peut aussi déclencher un custom event vers le module mais c'est lourd.
            // On va directement attraper l'instance via une solution de contournement (ou demander au comp de re-clear).
            // La solution la plus propre Stimulus est d'avoir un Outlets, mais on va plutôt utiliser un dispatch inverse :
            const dropzoneController = this.application.getControllerForElementAndIdentifier(dropzoneControllerElement, 'common--dropzone');

            if (dropzoneController) {
                if (indexStr !== null) {
                    (dropzoneController as any).removeFileByIndex(parseInt(indexStr, 10));
                } else {
                    (dropzoneController as any).clearFiles(true);
                }
            }
        }
    }

    private showPreview() {
        if (this.hasPdfPreviewTarget) {
            this.pdfPreviewTarget.classList.remove('d-none');
        }
        if (this.hasPlaceholderTarget) {
            this.placeholderTarget.classList.add('d-none');
        }
    }

    private hidePreview() {
        if (this.hasPdfPreviewTarget) {
            this.pdfPreviewTarget.classList.add('d-none');
        }
        if (this.hasPlaceholderTarget) {
            this.placeholderTarget.classList.remove('d-none');
        }
        if (this.hasNavTabsTarget) {
            this.navTabsTarget.innerHTML = '';
        }
        if (this.hasTabContentTarget) {
            this.tabContentTarget.innerHTML = '';
        }
    }

    disconnect() {
        this.managers = [];
        this.uploadedFiles = {};
    }
}
