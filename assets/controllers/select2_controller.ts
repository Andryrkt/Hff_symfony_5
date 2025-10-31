import { Controller } from "@hotwired/stimulus";
import $ from "jquery";

export default class extends Controller {
    private groupSelectionEnabled: boolean = true;
    private isInitialized: boolean = false;
    private originalOptions: any[] = [];

    static values = {
        groupSelection: { type: Boolean, default: true }
    };

    declare groupSelectionValue: boolean;

    connect() {
        console.log('Select2 permissions controller connected');
        
        this.groupSelectionEnabled = this.groupSelectionValue;
        this.initializeSelect2();
    }

    initializeSelect2() {
        if (this.isInitialized) {
            $(this.element).select2('destroy');
        }

        const element = this.element as HTMLSelectElement;
        const placeholder = element.dataset.placeholder || "Sélectionnez des permissions...";

        // Sauvegarder les options originales
        this.saveOriginalOptions();

        const config: any = {
            theme: "bootstrap5",
            width: "100%",
            placeholder: placeholder,
            allowClear: true,
            closeOnSelect: false,
            
            templateResult: this.formatOption.bind(this),
            templateSelection: this.formatSelection.bind(this),
            
            dropdownCssClass: `custom-select2-dropdown ${this.groupSelectionEnabled ? 'group-selection-enabled' : 'simple-selection-enabled'}`,
            containerCssClass: "custom-select2-container permissions-select2",
        };

        $(this.element).select2(config);

        if (this.groupSelectionEnabled) {
            this.setupGroupSelectionMode();
        }

        this.isInitialized = true;
        this.setupSymfonyIntegration();
    }

    saveOriginalOptions() {
        const $select = $(this.element);
        this.originalOptions = [];
        
        $select.find('optgroup').each((index, optgroup) => {
            const $optgroup = $(optgroup);
            const groupData = {
                label: $optgroup.attr('label'),
                options: [] as any[]
            };
            
            $optgroup.find('option').each((optIndex, option) => {
                const $option = $(option);
                groupData.options.push({
                    value: $option.attr('value'),
                    text: $option.text(),
                    selected: $option.is(':selected'),
                    disabled: $option.is(':disabled')
                });
            });
            
            this.originalOptions.push(groupData);
        });
    }

    setupGroupSelectionMode() {
        const $select = $(this.element);
        
        $select.off('select2:open.groupSelection').on('select2:open.groupSelection', () => {
            setTimeout(() => this.setupGroupSelection(), 50);
        });

        $select.off('change.groupSelection').on('change.groupSelection', () => {
            setTimeout(() => this.updateAllGroupsSelectionState(), 50);
        });

        // Gérer la recherche
        $select.off('select2:searching.groupSelection').on('select2:searching.groupSelection', (e) => {
            setTimeout(() => this.handleSearch(), 50);
        });

        // Gérer la fermeture de la recherche
        $select.off('select2:closing.groupSelection').on('select2:closing.groupSelection', () => {
            setTimeout(() => this.handleSearchClose(), 50);
        });

        setTimeout(() => this.updateAllGroupsSelectionState(), 100);
    }

    handleSearch() {
        if (!this.groupSelectionEnabled) return;

        const $select = $(this.element);
        const select2 = $select.data('select2');
        
        if (!select2 || !select2.$dropdown) return;

        const searchTerm = select2.$dropdown.find('.select2-search__field').val() as string;
        
        if (searchTerm && searchTerm.length > 0) {
            // En mode recherche, désactiver temporairement la sélection par groupe
            this.deactivateGroupSelection();
        } else {
            // Quand la recherche est vide, réactiver la sélection par groupe
            setTimeout(() => this.setupGroupSelection(), 50);
        }
    }

    handleSearchClose() {
        // Réactiver la sélection par groupe quand la recherche se ferme
        setTimeout(() => {
            if (this.groupSelectionEnabled) {
                this.setupGroupSelection();
            }
        }, 100);
    }

    setupGroupSelection() {
        if (!this.groupSelectionEnabled) return;

        const $select = $(this.element);
        const select2 = $select.data('select2');
        
        if (!select2 || !select2.$dropdown) return;

        const $dropdown = select2.$dropdown;
        const searchTerm = $dropdown.find('.select2-search__field').val() as string;
        
        // Ne pas activer la sélection par groupe si une recherche est en cours
        if (searchTerm && searchTerm.length > 0) {
            this.deactivateGroupSelection();
            return;
        }

        $select.find('optgroup').each((index, optgroup) => {
            const optgroupElement = optgroup as HTMLOptGroupElement;
            const optgroupLabel = optgroupElement.label;
            const $optgroupHeader = $dropdown.find('.select2-results__group').filter(function() {
                return $(this).text() === optgroupLabel;
            });
            
            if ($optgroupHeader.length && !$optgroupHeader.hasClass('clickable-optgroup')) {
                $optgroupHeader.addClass('clickable-optgroup');
                this.updateGroupSelectionState($optgroupHeader, $(optgroup));
                
                $optgroupHeader.off('click.select2optgroup').on('click.select2optgroup', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    this.toggleGroupSelection($(optgroup));
                });
            }
        });
    }

    deactivateGroupSelection() {
        const $select = $(this.element);
        const select2 = $select.data('select2');
        
        if (!select2 || !select2.$dropdown) return;

        const $dropdown = select2.$dropdown;
        
        $dropdown.find('.clickable-optgroup')
            .removeClass('clickable-optgroup partially-selected fully-selected no-selection')
            .off('click.select2optgroup');
    }

    toggleGroupSelection($optgroup: JQuery) {
        const $select = $(this.element);
        const $optionsInGroup = $optgroup.children('option').not(':disabled');
        const allSelectedInGroup = $optionsInGroup.toArray().every(option => $(option).is(':selected'));
        
        if (allSelectedInGroup) {
            $optionsInGroup.prop('selected', false).trigger('change');
        } else {
            $optionsInGroup.prop('selected', true).trigger('change');
        }
        
        $select.trigger('change.select2');
        
        // Mettre à jour l'état visuel
        setTimeout(() => {
            this.updateGroupSelectionInDropdown($optgroup.attr('label') || '');
            $select.select2('open');
        }, 50);
    }

    updateGroupSelectionState($header: JQuery, $optgroup: JQuery) {
        const $options = $optgroup.children('option').not(':disabled');
        const selectedCount = $options.filter(':selected').length;
        const totalCount = $options.length;
        
        $header.removeClass('no-selection partially-selected fully-selected');
        
        if (selectedCount === 0) {
            $header.addClass('no-selection');
        } else if (selectedCount === totalCount) {
            $header.addClass('fully-selected');
        } else {
            $header.addClass('partially-selected');
        }
    }

    updateGroupSelectionInDropdown(groupLabel: string) {
        const $select = $(this.element);
        const select2 = $select.data('select2');
        
        if (!select2 || !select2.$dropdown) return;

        const $dropdown = select2.$dropdown;
        const $optgroupHeader = $dropdown.find('.select2-results__group').filter(function() {
            return $(this).text() === groupLabel;
        });
        
        if ($optgroupHeader.length) {
            const $optgroup = $select.find(`optgroup[label="${groupLabel}"]`);
            if ($optgroup.length) {
                this.updateGroupSelectionState($optgroupHeader, $optgroup);
            }
        }
    }

    updateAllGroupsSelectionState() {
        if (!this.groupSelectionEnabled) return;

        const $select = $(this.element);
        const select2 = $select.data('select2');
        
        if (!select2 || !select2.$dropdown) return;

        const $dropdown = select2.$dropdown;
        
        $select.find('optgroup').each((index, optgroup) => {
            const optgroupElement = optgroup as HTMLOptGroupElement;
            const optgroupLabel = optgroupElement.label;
            const $optgroupHeader = $dropdown.find('.select2-results__group').filter(function() {
                return $(this).text() === optgroupLabel;
            });
            
            if ($optgroupHeader.length) {
                this.updateGroupSelectionState($optgroupHeader, $(optgroup));
            }
        });
    }

    // Méthodes de contrôle public
    selectAll() {
        const $select = $(this.element);
        $select.find('option').not(':disabled').prop('selected', true);
        $select.trigger('change');
        setTimeout(() => this.updateAllGroupsSelectionState(), 100);
    }

    deselectAll() {
        const $select = $(this.element);
        $select.find('option').prop('selected', false);
        $select.trigger('change');
        setTimeout(() => this.updateAllGroupsSelectionState(), 100);
    }

    selectGroupByLabel(groupLabel: string) {
        const $select = $(this.element);
        const $optgroup = $select.find(`optgroup[label="${groupLabel}"]`);
        if ($optgroup.length) {
            $optgroup.children('option').not(':disabled').prop('selected', true);
            $select.trigger('change');
            setTimeout(() => this.updateGroupSelectionInDropdown(groupLabel), 100);
        }
    }

    // Alternative : méthode pour gérer la sélection de groupe même pendant la recherche
    selectGroupByLabelSmart(groupLabel: string) {
        const $select = $(this.element);
        const select2 = $select.data('select2');
        
        if (!select2) return;

        // Vérifier si une recherche est en cours
        const searchTerm = select2.$dropdown?.find('.select2-search__field').val() as string;
        
        if (searchTerm && searchTerm.length > 0) {
            // En mode recherche, sélectionner seulement les options visibles du groupe
            const $visibleOptions = select2.$dropdown?.find('.select2-results__option:visible')
                .filter((index, element) => {
                    const $option = $(element);
                    const optionText = $option.text();
                    const optionValue = $option.data('data')?.element?.value;
                    
                    // Trouver l'option originale pour vérifier son groupe
                    for (const group of this.originalOptions) {
                        if (group.label === groupLabel) {
                            const optionInGroup = group.options.find((opt: any) => 
                                opt.value === optionValue || opt.text === optionText
                            );
                            if (optionInGroup) return true;
                        }
                    }
                    return false;
                });
            
            if ($visibleOptions && $visibleOptions.length > 0) {
                $visibleOptions.each((index, element) => {
                    const optionValue = $(element).data('data')?.element?.value;
                    if (optionValue) {
                        $select.find(`option[value="${optionValue}"]`).prop('selected', true);
                    }
                });
                $select.trigger('change');
            }
        } else {
            // Mode normal : sélectionner tout le groupe
            this.selectGroupByLabel(groupLabel);
        }
    }

    getSelectedPermissions(): string[] {
        return $(this.element).val() as string[];
    }

    getSelectedPermissionsCount(): number {
        return this.getSelectedPermissions().length;
    }

    setupSymfonyIntegration() {
        const $select = $(this.element);
        
        $select.on('change', () => {
            setTimeout(() => {
                const event = new Event('change', { bubbles: true });
                this.element.dispatchEvent(event);
            }, 0);
        });
    }

    formatOption(option: any) {
        if (!option.id) {
            return option.text;
        }
        
        const isDisabled = option.disabled || (option.element && option.element.disabled);
        const disabledClass = isDisabled ? 'disabled-option' : '';
        
        const description = option.element && option.element.getAttribute('data-description');
        const descriptionHtml = description ? 
            `<small class="text-muted d-block">${description}</small>` : '';
        
        const $option = $(
            `<span class="select2-option-custom ${disabledClass}">
                <span class="option-text">${option.text}</span>
                ${descriptionHtml}
            </span>`
        );
        
        return $option;
    }

    formatSelection(option: any) {
        if (!option.id) {
            return option.text;
        }
        
        return option.text;
    }

    disconnect() {
        const $select = $(this.element);
        $select.off('.groupSelection');
        if (this.isInitialized) {
            $select.select2('destroy');
        }
    }
}