import { Controller } from "@hotwired/stimulus";

/**
 * Contrôleur générique de prévisualisation PDF en onglets.
 * Peut être utilisé sur n'importe quelle page qui affiche des PDFs uploadés en onglets.
 *
 * Targets attendus dans le HTML :
 *   - pdfPreview   : conteneur principal (affiché/caché)
 *   - placeholder  : zone affichée quand aucun PDF n'est chargé
 *   - navTabs      : <ul class="nav"> qui reçoit les onglets
 *   - tabContent   : conteneur des panes
 */
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

    /** Map : fileKey → { title, dataUrl } */
    private uploadedFiles: Record<string, { title: string; dataUrl: string }> = {};

    connect() {
        console.log("✅ PdfPreviewController connecté");
    }

    /** Ajoute ou remplace un fichier single dans la map et re-render */
    addFile(id: string, title: string, file: File) {
        console.log(`[PDF Preview] addFile: ${title} (${id})`);
        
        // Nettoyer l'ancien URL si existant pour libérer la mémoire
        if (this.uploadedFiles[id]?.dataUrl.startsWith('blob:')) {
            URL.revokeObjectURL(this.uploadedFiles[id].dataUrl);
        }

        const blobUrl = URL.createObjectURL(file);
        this.uploadedFiles[id] = { title, dataUrl: blobUrl };
        this.renderTabs();
    }

    /** Ajoute plusieurs fichiers (mode multiple) et re-render */
    addFiles(id: string, titlePrefix: string, files: File[]) {
        console.log(`[PDF Preview] addFiles: ${files.length} fichiers pour ${id}`);
        // Supprime les anciens fichiers de cet id
        Object.keys(this.uploadedFiles).forEach(key => {
            if (key.startsWith(`${id}_`)) {
                if (this.uploadedFiles[key].dataUrl.startsWith('blob:')) {
                    URL.revokeObjectURL(this.uploadedFiles[key].dataUrl);
                }
                delete this.uploadedFiles[key];
            }
        });

        files.forEach((file, index) => {
            const blobUrl = URL.createObjectURL(file);
            this.uploadedFiles[`${id}_${index}`] = {
                title: `${titlePrefix} ${index + 1} (${file.name})`,
                dataUrl: blobUrl,
            };
        });
        this.renderTabs();
    }

    /** Supprime un fichier (ou plusieurs si multiple) et re-render */
    removeFile(id: string, isMultiple = false) {
        if (isMultiple) {
            Object.keys(this.uploadedFiles).forEach(key => {
                if (key.startsWith(`${id}_`)) delete this.uploadedFiles[key];
            });
        } else {
            delete this.uploadedFiles[id];
        }
        this.renderTabs();
    }

    /** Vide tout */
    clearAll() {
        this.uploadedFiles = {};
        this.hidePreview();
    }

    // ─── Actions Stimulus ────────────────────────────────────────────────────

    switchTab(event: Event) {
        event.preventDefault();
        const clickedTab = event.currentTarget as HTMLElement;
        const targetId = clickedTab.getAttribute('data-id');

        if (!targetId || !this.hasNavTabsTarget || !this.hasTabContentTarget) return;

        this.navTabsTarget.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active', 'text-primary', 'bg-white', 'fw-bold');
            link.classList.add('text-white');
        });

        this.tabContentTarget.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active', 'show');
            pane.classList.add('d-none');
        });

        clickedTab.classList.add('active', 'text-primary', 'bg-white', 'fw-bold');
        clickedTab.classList.remove('text-white');

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

        console.log(`[PDF Preview] Tentative de suppression de l'onglet: ${targetId}`);

        let managerBaseId = targetId;
        let indexStr: string | null = null;

        if (targetId.includes('_')) {
            const parts = targetId.split('_');
            managerBaseId = parts[0];
            indexStr = parts[1];
        }

        const dropzoneId = `dropzone-${managerBaseId}`;
        const dropzoneEl = document.getElementById(dropzoneId);
        
        if (dropzoneEl) {
            console.log(`[PDF Preview] Dropzone trouvée: ${dropzoneId}`);
            const dropzoneController = this.application.getControllerForElementAndIdentifier(dropzoneEl, 'common--dropzone');
            if (dropzoneController) {
                console.log(`[PDF Preview] Contrôleur dropzone trouvé, appel de la suppression`);
                if (indexStr !== null) {
                    (dropzoneController as any).removeFileByIndex(parseInt(indexStr, 10));
                } else {
                    (dropzoneController as any).clearFiles(true);
                }
            } else {
                console.error(`[PDF Preview] Contrôleur 'common--dropzone' non trouvé sur l'élément #${dropzoneId}`);
            }
        } else {
            console.error(`[PDF Preview] Élément #${dropzoneId} non trouvé dans le DOM`);
        }
    }

    // ─── Rendu interne ────────────────────────────────────────────────────────

    private renderTabs() {
        const ids = Object.keys(this.uploadedFiles).sort();

        if (ids.length === 0) {
            this.hidePreview();
            return;
        }

        this.showPreview();

        if (!this.hasNavTabsTarget || !this.hasTabContentTarget) return;

        // Conserver l'onglet actif si possible
        let activeId = ids[0];
        const currentActiveTab = this.navTabsTarget.querySelector('.nav-link.active');
        if (currentActiveTab) {
            const currentId = currentActiveTab.getAttribute('data-id');
            if (currentId && this.uploadedFiles[currentId]) activeId = currentId;
        }

        this.navTabsTarget.innerHTML = '';
        this.tabContentTarget.innerHTML = '';

        ids.forEach(id => {
            const data = this.uploadedFiles[id];
            const isActive = id === activeId;

            // Onglet
            const li = document.createElement('li');
            li.className = 'nav-item position-relative d-flex align-items-center';

            const a = document.createElement('a');
            a.className = `nav-link border-0 rounded-top pe-4 ${isActive ? 'active text-primary fw-bold bg-white' : 'text-white'}`;
            a.style.cursor = 'pointer';
            a.textContent = data.title;
            a.setAttribute('data-id', id);
            a.setAttribute('data-action', `click->common--pdf-preview#switchTab`);

            // Bouton fermer
            const closeBtn = document.createElement('span');
            closeBtn.className = 'position-absolute top-50 end-0 translate-middle-y me-2 text-danger';
            closeBtn.style.cursor = 'pointer';
            closeBtn.style.fontSize = '1.1rem';
            closeBtn.style.zIndex = '10';
            closeBtn.innerHTML = '<i class="fas fa-trash"></i>';
            closeBtn.setAttribute('data-id', id);
            closeBtn.setAttribute('data-action', `click->common--pdf-preview#removePreviewTab`);

            li.appendChild(a);
            li.appendChild(closeBtn);
            this.navTabsTarget.appendChild(li);

            // Pane
            const div = document.createElement('div');
            div.className = `tab-pane h-100 ${isActive ? 'active show' : 'd-none'}`;
            div.style.minHeight = '750px';
            div.setAttribute('data-pane-id', id);

            // Balise <object> avec fallback <embed> : la méthode la plus robuste
            const object = document.createElement('object');
            object.data = data.dataUrl;
            object.type = 'application/pdf';
            object.style.width = '100%';
            object.style.height = '750px';
            
            const embedFallback = document.createElement('embed');
            embedFallback.src = data.dataUrl;
            embedFallback.type = 'application/pdf';
            object.appendChild(embedFallback);

            div.appendChild(object);
            this.tabContentTarget.appendChild(div);
        });
        console.log(`[PDF Preview] Rendu terminé. Onglets : ${ids.length}`);
    }

    private showPreview() {
        console.log("[PDF Preview] Affichage de la zone de prévisualisation");
        if (this.hasPdfPreviewTarget) this.pdfPreviewTarget.classList.remove('d-none');
        if (this.hasPlaceholderTarget) this.placeholderTarget.classList.add('d-none');
    }

    private hidePreview() {
        console.log("[PDF Preview] Masquage de la zone de prévisualisation");
        if (this.hasPdfPreviewTarget) this.pdfPreviewTarget.classList.add('d-none');
        if (this.hasPlaceholderTarget) this.placeholderTarget.classList.remove('d-none');
        if (this.hasNavTabsTarget) this.navTabsTarget.innerHTML = '';
        if (this.hasTabContentTarget) this.tabContentTarget.innerHTML = '';
    }
}
