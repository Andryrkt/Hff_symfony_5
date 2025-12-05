// assets/controllers/components/tom_select_controller.ts

import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

export default class extends Controller<HTMLSelectElement> {
    private tomSelect?: TomSelect;

    connect() {
        // Protection Turbo
        if ((this.element as any).tomselect) return;

        const isMultiple = this.element.multiple;

        // Configuration compatible avec TomSelect 2.3+
        const settings: Record<string, any> = {
            plugins: isMultiple
                ? ['remove_button', 'clear_button']
                : [],
            placeholder: this.element.dataset.placeholder ?? undefined,
            closeAfterSelect: !isMultiple,
            maxOptions: 1000,
        };

        // Initialisation
        this.tomSelect = new TomSelect(this.element, settings);

        // Retirer l'attribut required de l'input créé par TomSelect
        setTimeout(() => {
            const wrapper = this.tomSelect?.wrapper;
            if (wrapper) {
                const tsInput = wrapper.querySelector('.ts-control input');
                if (tsInput) {
                    tsInput.removeAttribute('required');
                }
            }
        }, 0);

        // Synchroniser le <select> natif à chaque changement
        this.tomSelect.on('change', () => {
            this.syncNativeSelect();
        });

        // Active la magie des groupes pour toutes les sélections multiples
        if (isMultiple) {
            this.tomSelect.on('initialize', () => this.setupGroupSelection());
            this.tomSelect.on('dropdown_open', () => this.setupGroupSelection());
        }
    }

    disconnect() {
        this.tomSelect?.destroy();
    }

    // Gestion des optgroups cliquables + compteur
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

        if (this.tomSelect.dropdown_content.dataset.delegationBound === 'true') {
            return;
        }

        headers.forEach(header => {
            const h = header as HTMLElement;
            h.style.cursor = 'pointer';
            h.style.userSelect = 'none';
            h.title = 'Cliquer pour tout sélectionner/désélectionner';
        });

        const handleDropdownClick = (e: Event) => {
            const target = e.target as HTMLElement;
            const header = target.closest('.optgroup-header') as HTMLElement;

            if (!header) return;

            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

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

    // Synchro avec le vrai <select>
    private syncNativeSelect() {
        if (!this.tomSelect) return;

        const value = this.tomSelect.getValue();
        const selectedValues = Array.isArray(value) ? value : (value ? [value] : []);
        const selected = new Set(selectedValues);

        Array.from(this.element.options).forEach(option => {
            if (selected.has(option.value)) {
                option.selected = true;
                option.setAttribute('selected', 'selected');
            } else {
                option.selected = false;
                option.removeAttribute('selected');
            }
        });

        this.element.dispatchEvent(new Event('change', { bubbles: true }));

        if (selectedValues.length > 0) {
            this.element.setCustomValidity('');
        }
    }
}
