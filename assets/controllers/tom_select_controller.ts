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

        // Active la magie des groupes pour toutes les sélections multiples
        if (isMultiple) {
            console.log('🎬 Configuration des événements pour sélection multiple');

            // Initialiser dès que TomSelect est prêt
            this.tomSelect.on('initialize', () => {
                console.log('📢 Événement: initialize');
                this.setupGroupSelection();
            });

            // Essayer différents noms d'événements pour l'ouverture du dropdown
            this.tomSelect.on('dropdown_open', () => {
                console.log('📢 Événement: dropdown_open');
                this.setupGroupSelection();
            });

            this.tomSelect.on('type', () => {
                console.log('📢 Événement: type');
                this.setupGroupSelection();
            });

            this.tomSelect.on('focus', () => {
                console.log('📢 Événement: focus');
                this.setupGroupSelection();
            });

            // Forcer l'initialisation immédiate
            setTimeout(() => {
                console.log('⏰ Initialisation forcée après timeout');
                this.setupGroupSelection();
            }, 100);
        }
    }

    disconnect() {
        this.tomSelect?.destroy();
    }

    // ================================================================
    // Gestion des optgroups cliquables + compteur
    // ================================================================
    private setupGroupSelection() {
        console.log('🔧 setupGroupSelection() appelé');

        if (!this.tomSelect) {
            console.log('⚠️ TomSelect non disponible');
            return;
        }

        console.log('📍 Dropdown content:', this.tomSelect.dropdown_content);

        this.updateGroupHeaders();
        this.bindGroupHeaders();

        // Mise à jour après chaque ajout/suppression
        this.tomSelect.off('item_add item_remove');
        this.tomSelect.on('item_add', () => this.refreshGroupState());
        this.tomSelect.on('item_remove', () => this.refreshGroupState());
    }

    private refreshGroupState() {
        this.updateGroupHeaders();
        this.syncNativeSelect();
    }

    private updateGroupHeaders() {
        if (!this.tomSelect?.dropdown_content) {
            console.log('⚠️ Dropdown content non disponible');
            return;
        }

        const headers = this.tomSelect.dropdown_content.querySelectorAll('.optgroup-header');
        console.log(`🔍 Nombre de groupes trouvés: ${headers.length}`);

        if (headers.length === 0) {
            console.log('⚠️ Aucun groupe trouvé dans le dropdown');
            return;
        }

        const selected = new Set(this.tomSelect.items);
        console.log(`📊 Éléments sélectionnés:`, Array.from(selected));

        headers.forEach(header => {
            const h = header as HTMLElement;
            const group = h.closest('.optgroup');
            if (!group) return;

            const values = Array.from(group.querySelectorAll('.option'))
                .map(el => el.getAttribute('data-value'))
                .filter(Boolean) as string[];

            const selectedCount = values.filter(v => selected.has(v)).length;
            const total = values.length;

            console.log(`📁 Groupe "${h.textContent?.trim()}": ${selectedCount}/${total} sélectionnés`);

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
        if (!this.tomSelect?.dropdown_content) {
            console.log('⚠️ Dropdown content non disponible pour bindGroupHeaders');
            return;
        }

        const headers = this.tomSelect.dropdown_content.querySelectorAll('.optgroup-header');
        console.log(`🔗 Attachement des événements à ${headers.length} en-têtes de groupe`);

        // Vérifier si l'événement est déjà attaché au dropdown
        if (this.tomSelect.dropdown_content.dataset.delegationBound === 'true') {
            console.log('⏭️ Délégation déjà configurée sur le dropdown');
            return;
        }

        // Appliquer les styles à tous les en-têtes
        headers.forEach((header, index) => {
            const h = header as HTMLElement;
            h.style.cursor = 'pointer';
            h.style.userSelect = 'none';
            h.title = 'Cliquer pour tout sélectionner/désélectionner';
            console.log(`✅ Style appliqué à l'en-tête ${index + 1}: "${h.textContent?.trim()}"`);
        });

        // DÉLÉGATION D'ÉVÉNEMENTS : Attacher l'événement au dropdown parent
        const handleDropdownClick = (e: Event) => {
            const target = e.target as HTMLElement;

            // Vérifier si le clic est sur un en-tête de groupe ou un de ses enfants
            const header = target.closest('.optgroup-header') as HTMLElement;

            if (!header) {
                return; // Pas un clic sur un en-tête
            }

            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            console.log(`🖱️ CLIC DÉTECTÉ (délégation) sur: "${header.textContent?.trim()}"`);

            const group = header.closest('.optgroup');
            if (!group || !this.tomSelect) {
                console.log('⚠️ Groupe ou TomSelect non trouvé');
                return;
            }

            const values = Array.from(group.querySelectorAll('.option'))
                .map(el => el.getAttribute('data-value'))
                .filter(Boolean) as string[];

            if (values.length === 0) {
                console.log('⚠️ Aucune option dans ce groupe');
                return;
            }

            const allSelected = values.every(v => this.tomSelect!.items.includes(v));
            console.log(`📊 Action: ${allSelected ? 'DÉSÉLECTIONNER' : 'SÉLECTIONNER'} ${values.length} éléments`);

            if (allSelected) {
                values.forEach(v => this.tomSelect!.removeItem(v, true));
            } else {
                values.forEach(v => this.tomSelect!.addItem(v, true));
            }

            this.tomSelect!.refreshItems();
            this.refreshGroupState();
        };

        // Attacher l'événement au dropdown parent avec la phase de capture
        this.tomSelect.dropdown_content.addEventListener('mousedown', handleDropdownClick, true);
        this.tomSelect.dropdown_content.addEventListener('click', handleDropdownClick, true);
        this.tomSelect.dropdown_content.dataset.delegationBound = 'true';

        console.log(`🎯 Délégation d'événements configurée sur le dropdown`);
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