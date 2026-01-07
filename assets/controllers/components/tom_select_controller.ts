// assets/controllers/components/tom_select_controller.ts

import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

export default class extends Controller<HTMLSelectElement> {
    private tomSelect?: TomSelect;

    connect() {
        // Protection Turbo/Double init
        if ((this.element as any).tomselect) return;

        const isMultiple = this.element.multiple;

        // Configuration compatible avec TomSelect 2.4+
        const settings: any = {
            plugins: isMultiple
                ? ['remove_button', 'clear_button']
                : ['dropdown_input'], // On ajoute dropdown_input car le CSS du projet semble le cibler
            placeholder: this.element.dataset.placeholder || undefined,
            closeAfterSelect: !isMultiple,
            maxOptions: 1000,
            allowEmptyOption: true,
            // Permettre la recherche même sur de petites listes
            onInitialize: function () {
                // Peut être utilisé pour des ajustements post-init
            }
        };

        try {
            // Initialisation
            this.tomSelect = new TomSelect(this.element, settings);

            // Retirer l'attribut required de l'input créé par TomSelect pour éviter les bulles HTML5 mal placées
            setTimeout(() => {
                const wrapper = this.tomSelect?.wrapper;
                if (wrapper) {
                    const tsInput = wrapper.querySelector('input');
                    if (tsInput) {
                        tsInput.removeAttribute('required');
                    }
                }
            }, 0);

            // Synchroniser le <select> natif à chaque changement dans TomSelect
            this.tomSelect.on('change', () => {
                this.syncNativeSelect();
            });

            // Active la magie des groupes pour toutes les sélections multiples
            if (isMultiple) {
                this.tomSelect.on('initialize', () => this.setupGroupSelection());
                this.tomSelect.on('dropdown_open', () => this.setupGroupSelection());
            }

            // Listener pour la mise à jour des options depuis l'extérieur (ex: AgenceServiceCasierManager)
            this.element.addEventListener('options-updated', () => {
                this.updateOptionsFromNative();
            });

            // Synchroniser TomSelect quand le <select> natif change de valeur (via script externe)
            this.element.addEventListener('change', (e: any) => {
                // Ignorer si l'événement vient de TomSelect lui-même pour éviter les boucles
                if (e.detail && e.detail.fromTomSelect) return;

                if (this.tomSelect) {
                    const value = this.getSelectedValue();
                    this.tomSelect.setValue(value, true); // true = silent
                }
            });

            // Si le select est déjà rempli au chargement, on s'assure que TomSelect est à jour
            if (this.element.options.length > 0) {
                this.updateOptionsFromNative();
            }

        } catch (error) {
            console.error('TomSelect initialization failed:', error);
        }
    }

    disconnect() {
        if (this.tomSelect) {
            this.tomSelect.destroy();
            delete (this.element as any).tomselect;
        }
    }

    /**
     * Met à jour les options de TomSelect à partir de celles du select natif
     */
    private updateOptionsFromNative() {
        if (!this.tomSelect) return;

        const value = this.getSelectedValue();
        const isMultiple = this.element.multiple;

        this.tomSelect.clearOptions();

        Array.from(this.element.children).forEach(child => {
            if (child.tagName === 'OPTGROUP') {
                const group = child as HTMLOptGroupElement;
                Array.from(group.children).forEach((opt: HTMLOptionElement) => {
                    if (opt.value !== undefined) {
                        this.tomSelect!.addOption({
                            value: opt.value,
                            text: opt.text,
                            optgroup: group.label
                        });
                    }
                });
            } else if (child.tagName === 'OPTION') {
                const opt = child as HTMLOptionElement;
                if (opt.value !== undefined && opt.value !== "") {
                    this.tomSelect!.addOption({ value: opt.value, text: opt.text });
                }
            }
        });

        this.tomSelect.setValue(value, true);
        this.tomSelect.refreshOptions(false);

        if (this.element.disabled) {
            this.tomSelect.disable();
        } else {
            this.tomSelect.enable();
        }
    }

    private getSelectedValue(): string | string[] {
        if (this.element.multiple) {
            return Array.from(this.element.selectedOptions).map(o => o.value);
        }
        return this.element.value;
    }

    private syncNativeSelect() {
        if (!this.tomSelect) return;

        const value = this.tomSelect.getValue();
        const selectedValues = Array.isArray(value) ? value : (value ? [value] : []);
        const selectedSet = new Set(selectedValues);

        Array.from(this.element.options).forEach(option => {
            if (selectedSet.has(option.value)) {
                option.selected = true;
                option.setAttribute('selected', 'selected');
            } else {
                option.selected = false;
                option.removeAttribute('selected');
            }
        });

        // Dispatch d'un événement personnalisé pour informer les autres scripts (ex: Manager)
        this.element.dispatchEvent(new CustomEvent('change', {
            bubbles: true,
            detail: { fromTomSelect: true }
        }));

        if (selectedValues.length > 0) {
            this.element.setCustomValidity('');
        }
    }

    // Gestion des optgroups cliquables + compteur (pour sélections multiples)
    private setupGroupSelection() {
        if (!this.tomSelect) return;
        this.updateGroupHeaders();
        this.bindGroupHeaders();
        this.tomSelect.off('item_add item_remove');
        this.tomSelect.on('item_add', () => this.refreshGroupState());
        this.tomSelect.on('item_remove', () => this.refreshGroupState());
    }

    private refreshGroupState() {
        this.updateGroupHeaders();
        this.syncNativeSelect();
    }

    private updateGroupHeaders() {
        if (!this.tomSelect?.dropdown_content) return;
        const headers = this.tomSelect.dropdown_content.querySelectorAll('.optgroup-header');
        if (headers.length === 0) return;
        const selected = new Set(this.tomSelect.items);

        headers.forEach(header => {
            const h = header as HTMLElement;
            const group = h.closest('.optgroup');
            if (!group) return;
            const values = Array.from(group.querySelectorAll('.option'))
                .map(el => el.getAttribute('data-value'))
                .filter(Boolean) as string[];
            const selectedCount = values.filter(v => selected.has(v)).length;
            const total = values.length;
            h.classList.toggle('fully-selected', selectedCount === total && total > 0);
            h.classList.toggle('partially-selected', selectedCount > 0 && selectedCount < total);
            h.classList.toggle('no-selection', selectedCount === 0);
            let count = h.querySelector('.group-count');
            if (!count) {
                count = document.createElement('small');
                count.className = 'group-count text-muted ms-2';
                h.appendChild(count);
            }
            count.textContent = total > 0 ? ` (${selectedCount}/${total})` : '';
        });
    }

    private bindGroupHeaders() {
        if (!this.tomSelect?.dropdown_content) return;
        const headers = this.tomSelect.dropdown_content.querySelectorAll('.optgroup-header');
        if (this.tomSelect.dropdown_content.dataset.delegationBound === 'true') return;

        headers.forEach(header => {
            const h = header as HTMLElement;
            h.style.cursor = 'pointer';
            h.style.userSelect = 'none';
        });

        const handleDropdownClick = (e: Event) => {
            const target = e.target as HTMLElement;
            const header = target.closest('.optgroup-header') as HTMLElement;
            if (!header) return;
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const group = header.closest('.optgroup');
            if (!group || !this.tomSelect) return;
            const values = Array.from(group.querySelectorAll('.option'))
                .map(el => el.getAttribute('data-value'))
                .filter(Boolean) as string[];
            if (values.length === 0) return;
            const allSelected = values.every(v => this.tomSelect!.items.includes(v));
            if (allSelected) {
                values.forEach(v => this.tomSelect!.removeItem(v, true));
            } else {
                values.forEach(v => this.tomSelect!.addItem(v, true));
            }
            this.tomSelect!.refreshItems();
            this.refreshGroupState();
        };

        this.tomSelect.dropdown_content.addEventListener('mousedown', handleDropdownClick, true);
        this.tomSelect.dropdown_content.addEventListener('click', handleDropdownClick, true);
        this.tomSelect.dropdown_content.dataset.delegationBound = 'true';
    }
}
