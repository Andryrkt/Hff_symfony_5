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

    /** Ajoute ou remplace un fichier single dans la map et re-render */
    addFile(id: string, title: string, file: File) {
        const reader = new FileReader();
        reader.onload = (e) => {
            if (e.target?.result) {
                this.uploadedFiles[id] = { title, dataUrl: e.target.result as string };
                this.renderTabs();
            }
        };
        reader.readAsDataURL(file);
    }

    /** Ajoute plusieurs fichiers (mode multiple) et re-render */
    addFiles(id: string, titlePrefix: string, files: File[]) {
        // Supprime les anciens fichiers de cet id
        Object.keys(this.uploadedFiles).forEach(key => {
            if (key.startsWith(`${id}_`)) delete this.uploadedFiles[key];
        });

        let loaded = 0;
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (e.target?.result) {
                    this.uploadedFiles[`${id}_${index}`] = {
                        title: `${titlePrefix} ${index + 1} (${file.name})`,
                        dataUrl: e.target.result as string,
                    };
                }
                loaded++;
                if (loaded === files.length) this.renderTabs();
            };
            reader.readAsDataURL(file);
        });
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

        let managerBaseId = targetId;
        let indexStr: string | null = null;

        if (targetId.includes('_')) {
            const parts = targetId.split('_');
            managerBaseId = parts[0];
            indexStr = parts[1];
        }

        const dropzoneEl = document.getElementById(`dropzone-${managerBaseId}`);
        if (dropzoneEl) {
            const dropzoneController = this.application.getControllerForElementAndIdentifier(dropzoneEl, 'common--dropzone');
            if (dropzoneController) {
                if (indexStr !== null) {
                    (dropzoneController as any).removeFileByIndex(parseInt(indexStr, 10));
                } else {
                    (dropzoneController as any).clearFiles(true);
                }
            }
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
            a.className = `nav-link text-black border-0 rounded-top pe-4 ${isActive ? 'active text-primary fw-bold bg-white' : ''}`;
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

    private showPreview() {
        this.pdfPreviewTarget?.classList.remove('d-none');
        this.placeholderTarget?.classList.add('d-none');
    }

    private hidePreview() {
        this.pdfPreviewTarget?.classList.add('d-none');
        this.placeholderTarget?.classList.remove('d-none');
        if (this.hasNavTabsTarget) this.navTabsTarget.innerHTML = '';
        if (this.hasTabContentTarget) this.tabContentTarget.innerHTML = '';
    }
}
