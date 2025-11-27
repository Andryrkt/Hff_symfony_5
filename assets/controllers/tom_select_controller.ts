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

        // Initial setup
        this.updateGroupSelectionStates();
        this.addGroupHeaderClickListeners();

        // Re-attach listeners and update state on changes
        this.tomSelect.on('change', () => {
            this.updateGroupSelectionStates();
        });

        // Re-attach listeners when dropdown opens (in case of re-render)
        this.tomSelect.on('dropdown_open', () => {
            this.addGroupHeaderClickListeners();
            this.updateGroupSelectionStates();
        });
    }

    updateGroupSelectionStates() {
        if (!this.tomSelect || !this.tomSelect.dropdown_content) {
            return;
        }

        const selectedItems = new Set(this.tomSelect.items);
        const dropdown = this.tomSelect.dropdown_content;
        const groupHeaders = dropdown.querySelectorAll('.optgroup-header');

        groupHeaders.forEach((header) => {
            const htmlHeader = header as HTMLElement;
            const groupLabel = htmlHeader.textContent?.trim().replace(/\s*\(\d+\/\d+\)$/, ''); // Remove existing count if any
            if (!groupLabel) return;

            // Find the parent optgroup div
            const optgroup = htmlHeader.closest('.optgroup');
            if (!optgroup) return;

            // Find all options in this group
            const options = optgroup.querySelectorAll('.option');
            const groupOptions: string[] = [];
            
            options.forEach(option => {
                const value = option.getAttribute('data-value');
                if (value) {
                    groupOptions.push(value);
                }
            });

            const totalOptions = groupOptions.length;
            if (totalOptions === 0) return;

            const selectedInGroup = groupOptions.filter(opt => selectedItems.has(opt)).length;

            // Update visual classes
            htmlHeader.classList.remove('fully-selected', 'partially-selected', 'no-selection');

            if (selectedInGroup === 0) {
                htmlHeader.classList.add('no-selection');
            } else if (selectedInGroup === totalOptions) {
                htmlHeader.classList.add('fully-selected');
            } else {
                htmlHeader.classList.add('partially-selected');
            }

            // Update count
            // First, restore original text without count
            const textNode = Array.from(htmlHeader.childNodes).find(node => node.nodeType === Node.TEXT_NODE);
            if (textNode) {
                textNode.textContent = groupLabel;
            }

            let countSpan = htmlHeader.querySelector('.selection-count');
            if (!countSpan) {
                countSpan = document.createElement('span');
                countSpan.className = 'selection-count';
                htmlHeader.appendChild(countSpan);
            }
            countSpan.textContent = ` (${selectedInGroup}/${totalOptions})`;
        });
    }

    addGroupHeaderClickListeners() {
        if (!this.tomSelect || !this.tomSelect.dropdown_content) {
            return;
        }

        const dropdown = this.tomSelect.dropdown_content;
        const groupHeaders = dropdown.querySelectorAll('.optgroup-header');

        groupHeaders.forEach(header => {
            const htmlHeader = header as HTMLElement;
            
            // Remove existing listener to avoid duplicates
            htmlHeader.removeEventListener('click', this.onGroupHeaderClick);
            htmlHeader.addEventListener('click', this.onGroupHeaderClick);
            
            htmlHeader.style.cursor = 'pointer';
            htmlHeader.style.userSelect = 'none';
        });
    }

    // Use arrow function to bind 'this' automatically
    onGroupHeaderClick = (event: Event) => {
        if (!this.tomSelect) return;

        event.preventDefault();
        event.stopPropagation();

        const header = event.currentTarget as HTMLElement;
        const optgroup = header.closest('.optgroup');
        if (!optgroup) return;

        const options = optgroup.querySelectorAll('.option');
        const groupOptions: string[] = [];
        
        options.forEach(option => {
            const value = option.getAttribute('data-value');
            if (value) {
                groupOptions.push(value);
            }
        });

        if (groupOptions.length === 0) return;

        const selectedItems = new Set(this.tomSelect.items);
        const allSelected = groupOptions.every(opt => selectedItems.has(opt));

        // Toggle selection
        if (allSelected) {
            groupOptions.forEach(opt => this.tomSelect.removeItem(opt, true));
        } else {
            groupOptions.forEach(opt => {
                if (!selectedItems.has(opt)) {
                    this.tomSelect.addItem(opt, true);
                }
            });
        }
        
        // Refresh items once at the end to improve performance
        this.tomSelect.refreshItems();
        this.updateGroupSelectionStates();
    }
}