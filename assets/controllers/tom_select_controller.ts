// assets/controllers/tom_select_controller.ts

import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';
import type { RecursivePartial, TomSettings } from 'tom-select/dist/types/types';

export default class extends Controller<HTMLSelectElement> {
    private tomSelect?: TomSelect;

    connect() {
        // Protection Turbo
        if ((this.element as any).tomselect) return;

        const isMultiple = this.element.multiple;
        const hasOptgroups = this.element.querySelector('optgroup') !== null;

        // Configuration compatible avec TomSelect 2.3+
        const settings: RecursivePartial<TomSettings> = {
            plugins: isMultiple
                ? ['remove_button', 'clear_button']
                : [],
            placeholder: this.element.dataset.placeholder ?? undefined,
            closeAfterSelect: !isMultiple,
            maxOptions: 1000,
        };

        // Initialisation
        this.tomSelect = new TomSelect(this.element, settings);

        // CRUCIAL : Retirer l'attribut required de l'input créé par TomSelect
        // TomSelect copie automatiquement le required du <select> vers son input de contrôle
        // On utilise un délai court pour s'assurer que l'input est bien créé
        setTimeout(() => {
            // Chercher l'input dans le wrapper TomSelect
            const wrapper = this.tomSelect?.wrapper;
            if (wrapper) {
                const tsInput = wrapper.querySelector('.ts-control input');
                if (tsInput) {
                    tsInput.removeAttribute('required');
                    console.log('✅ Attribut required retiré de l\'input TomSelect');
                }
            }
        }, 0);

        // CRUCIAL : Synchroniser le <select> natif à chaque changement
        // Cela garantit que la validation HTML5 et les autres contrôleurs fonctionnent correctement
        this.tomSelect.on('change', () => {
            this.syncNativeSelect();
        });

        // Active la magie des groupes uniquement si nécessaire
        if (isMultiple && hasOptgroups) {
            // On attend que le dropdown soit construit
            this.tomSelect.on('initialize', () => this.setupGroupSelection());
            this.tomSelect.on('dropdown_open', () => this.setupGroupSelection());
        }
    }

    disconnect() {
        this.tomSelect?.destroy();
    }

    // ================================================================
    // Gestion des optgroups cliquables + compteur
    // ================================================================
    private setupGroupSelection() {
        if (!this.tomSelect) return;

        this.updateGroupHeaders();
        this.bindGroupHeaders();

        // Mise à jour après chaque ajout/suppression
        this.tomSelect.on('item_add', () => this.refreshGroupState());
        this.tomSelect.on('item_remove', () => this.refreshGroupState());
    }

    private refreshGroupState() {
        this.updateGroupHeaders();
        this.syncNativeSelect();
    }

    private updateGroupHeaders() {
        if (!this.tomSelect?.dropdown_content) return;

        const selected = new Set(this.tomSelect.items);

        this.tomSelect.dropdown_content.querySelectorAll('.optgroup-header').forEach(header => {
            const h = header as HTMLElement;
            const group = h.closest('.optgroup');
            if (!group) return;

            const values = Array.from(group.querySelectorAll('.option'))
                .map(el => el.getAttribute('data-value'))
                .filter(Boolean) as string[];

            const selectedCount = values.filter(v => selected.has(v)).length;
            const total = values.length;

            // Classes
            h.classList.toggle('fully-selected', selectedCount === total && total > 0);
            h.classList.toggle('partially-selected', selectedCount > 0 && selectedCount < total);
            h.classList.toggle('no-selection', selectedCount === 0);

            // Compteur
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

        this.tomSelect.dropdown_content.querySelectorAll('.optgroup-header').forEach(header => {
            const h = header as HTMLElement;
            h.style.cursor = 'pointer';
            h.title = 'Tout sélectionner / désélectionner';

            // Nettoyage propre
            h.onclick = null;

            h.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const group = h.closest('.optgroup');
                if (!group || !this.tomSelect) return;

                const values = Array.from(group.querySelectorAll('.option'))
                    .map(el => el.getAttribute('data-value'))
                    .filter(Boolean) as string[];

                const allSelected = values.every(v => this.tomSelect!.items.includes(v));

                if (allSelected) {
                    values.forEach(v => this.tomSelect!.removeItem(v, true));
                } else {
                    values.forEach(v => this.tomSelect!.addItem(v, true));
                }

                this.tomSelect.refreshItems();
                this.refreshGroupState();
            });
        });
    }

    // ================================================================
    // Synchro avec le vrai <select> (obligatoire pour validation, Turbo, etc.)
    // ================================================================
    private syncNativeSelect() {
        if (!this.tomSelect) return;

        // Pour select simple : getValue() retourne une string
        // Pour select multiple : getValue() retourne un array
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

        // Déclencher l'événement change pour notifier les autres contrôleurs
        this.element.dispatchEvent(new Event('change', { bubbles: true }));

        // Effacer tout message de validation personnalisé si une valeur est sélectionnée
        if (selectedValues.length > 0) {
            this.element.setCustomValidity('');
        }
    }
}