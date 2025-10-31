import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

export default class extends Controller {
    private tomSelect!: TomSelect;

    connect() {
        // Prevent re-initialization by Turbo
        if ((this.element as any).tomselect) {
            return;
        }

        const settings: { [key: string]: any } = {};

        if (this.element.hasAttribute('data-placeholder')) {
            settings.placeholder = this.element.getAttribute('data-placeholder');
        }

        if (this.element.hasAttribute('multiple')) {
            settings.plugins = {
                remove_button: {
                    title: 'Retirer cet élément',
                },
            };
        }

        try {
            this.tomSelect = new TomSelect(this.element as HTMLSelectElement, settings);

            // Utiliser setTimeout pour laisser le DOM se mettre à jour
            setTimeout(() => {
                this.setupGroupSelection();
            }, 200);

        } catch (error) {
            console.error('Error initializing TomSelect:', error);
        }
    }

    disconnect() {
        if (this.tomSelect) {
            this.tomSelect.destroy();
        }
    }

    setupGroupSelection() {
        if (!this.tomSelect || !this.tomSelect.dropdown_content) {
            return;
        }

        this.updateGroupSelectionStates();
        this.addGroupHeaderClickListeners();

        // Réattacher les événements pour les mises à jour
        this.tomSelect.on('change', () => {
            this.updateGroupSelectionStates();
        });
    }

    getOptGroups() {
        // Récupérer les groupes depuis les optgroups du select original
        const optgroups: any[] = [];
        const originalOptgroups = (this.element as HTMLSelectElement).querySelectorAll('optgroup');
        
        originalOptgroups.forEach((optgroup, index) => {
            optgroups.push({
                id: optgroup.label || `group-${index}`,
                label: optgroup.label
            });
        });

        return optgroups;
    }

    getOptions() {
        // Récupérer les options depuis le select original
        const options: any[] = [];
        const originalOptions = (this.element as HTMLSelectElement).querySelectorAll('option');
        
        originalOptions.forEach((option) => {
            if (option.value) {
                const optgroup = option.closest('optgroup');
                const optionData: any = {
                    value: option.value,
                    text: option.textContent || option.value
                };

                if (optgroup && optgroup.label) {
                    optionData.group = optgroup.label;
                }

                options.push(optionData);
            }
        });

        return options;
    }

    updateGroupSelectionStates() {
        if (!this.tomSelect || !this.tomSelect.dropdown_content) {
            return;
        }

        const selectedItems = new Set(this.tomSelect.items);
        const dropdown = this.tomSelect.dropdown_content;

        // Sélectionner tous les en-têtes de groupe
        const groupHeaders = dropdown.querySelectorAll('.ts-dropdown-optgroup');

        groupHeaders.forEach((header) => {
            const htmlHeader = header as HTMLElement; // Conversion en HTMLElement
            const groupLabel = htmlHeader.textContent?.trim();
            if (!groupLabel) return;

            // Trouver toutes les options de ce groupe
            const groupOptions: string[] = [];
            const nextElement = htmlHeader.nextElementSibling;
            
            if (nextElement && nextElement.classList.contains('ts-dropdown-optgroup-content')) {
                const options = nextElement.querySelectorAll('.option');
                options.forEach(option => {
                    const value = option.getAttribute('data-value');
                    if (value) {
                        groupOptions.push(value);
                    }
                });
            }

            const totalOptions = groupOptions.length;
            if (totalOptions === 0) return;

            const selectedInGroup = groupOptions.filter(opt => selectedItems.has(opt)).length;

            // Mettre à jour les classes visuelles
            htmlHeader.classList.remove('fully-selected', 'partially-selected', 'no-selection');

            if (selectedInGroup === 0) {
                htmlHeader.classList.add('no-selection');
            } else if (selectedInGroup === totalOptions) {
                htmlHeader.classList.add('fully-selected');
            } else {
                htmlHeader.classList.add('partially-selected');
            }

            // Ajouter un indicateur visuel
            const countSpan = htmlHeader.querySelector('.selection-count') || document.createElement('span');
            countSpan.className = 'selection-count';
            countSpan.textContent = ` (${selectedInGroup}/${totalOptions})`;
            
            if (!htmlHeader.querySelector('.selection-count')) {
                htmlHeader.appendChild(countSpan);
            }
        });
    }

    addGroupHeaderClickListeners() {
        if (!this.tomSelect || !this.tomSelect.dropdown_content) {
            return;
        }

        const dropdown = this.tomSelect.dropdown_content;
        const groupHeaders = dropdown.querySelectorAll('.ts-dropdown-optgroup');

        groupHeaders.forEach(header => {
            const htmlHeader = header as HTMLElement; // Conversion en HTMLElement
            
            // Éviter les doublons d'écouteurs
            header.removeEventListener('click', this.onGroupHeaderClick.bind(this));
            header.addEventListener('click', this.onGroupHeaderClick.bind(this));
            
            // Style pour indiquer que c'est cliquable
            htmlHeader.style.cursor = 'pointer';
            htmlHeader.style.fontWeight = 'bold';
        });
    }

    onGroupHeaderClick(event: Event) {
        if (!this.tomSelect) return;

        const header = event.currentTarget as HTMLElement; // Déjà HTMLElement
        const dropdown = this.tomSelect.dropdown_content;
        
        if (!dropdown) return;

        // Trouver le contenu du groupe suivant
        const groupContent = header.nextElementSibling;
        if (!groupContent || !groupContent.classList.contains('ts-dropdown-optgroup-content')) {
            return;
        }

        // Récupérer toutes les valeurs du groupe
        const groupOptions: string[] = [];
        const options = groupContent.querySelectorAll('.option');
        options.forEach(option => {
            const value = option.getAttribute('data-value');
            if (value) {
                groupOptions.push(value);
            }
        });

        if (groupOptions.length === 0) return;

        const selectedItems = new Set(this.tomSelect.items);
        const allSelected = groupOptions.every(opt => selectedItems.has(opt));

        if (allSelected) {
            // Désélectionner tout le groupe
            groupOptions.forEach(opt => {
                this.tomSelect.removeItem(opt, true);
            });
        } else {
            // Sélectionner tout le groupe
            groupOptions.forEach(opt => {
                if (!selectedItems.has(opt)) {
                    this.tomSelect.addItem(opt);
                }
            });
        }

        // Mettre à jour l'état visuel
        setTimeout(() => {
            this.updateGroupSelectionStates();
        }, 50);
    }
}