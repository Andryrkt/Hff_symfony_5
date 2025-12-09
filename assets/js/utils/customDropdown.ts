/**
 * Gestion des dropdowns personnalisés
 * Alternative à Bootstrap pour les menus déroulants
 */

export class CustomDropdown {
    private dropdowns: NodeListOf<Element>;
    private activeDropdown: Element | null = null;

    constructor() {
        this.dropdowns = document.querySelectorAll('.custom-dropdown');
        this.init();
    }

    private init(): void {
        // Initialiser tous les dropdowns
        this.dropdowns.forEach(dropdown => {
            if (dropdown.hasAttribute('data-initialized')) {
                return;
            }

            const toggle = dropdown.querySelector('.custom-dropdown-toggle');
            const menu = dropdown.querySelector('.custom-dropdown-menu');

            if (toggle && menu) {
                this.setupDropdown(dropdown, toggle, menu);
                dropdown.setAttribute('data-initialized', 'true');
            }
        });

        // Fermer les dropdowns en cliquant à l'extérieur
        document.addEventListener('click', (e: MouseEvent) => {
            const target = e.target as Element;
            if (!target.closest('.custom-dropdown')) {
                this.closeAllDropdowns();
            }
        });

        // Fermer avec la touche Escape
        document.addEventListener('keydown', (e: KeyboardEvent) => {
            if (e.key === 'Escape') {
                this.closeAllDropdowns();
            }
        });
    }

    private setupDropdown(dropdown: Element, toggle: Element, menu: Element): void {
        toggle.addEventListener('click', (e: Event) => {
            e.preventDefault();
            e.stopPropagation();

            const isOpen = menu.classList.contains('show');

            // Fermer tous les autres dropdowns
            this.closeAllDropdowns();

            if (!isOpen) {
                this.openDropdown(dropdown, toggle, menu);
            }
        });

        // Gérer les sous-menus (dropstart)
        const nestedDropdowns = dropdown.querySelectorAll('.custom-dropstart');
        nestedDropdowns.forEach(nested => {
            const nestedToggle = nested.querySelector('.custom-dropdown-toggle');
            const nestedMenu = nested.querySelector('.custom-dropdown-menu');

            if (nestedToggle && nestedMenu) {
                this.setupNestedDropdown(nested, nestedToggle, nestedMenu);
            }
        });
    }

    private setupNestedDropdown(dropdown: Element, toggle: Element, menu: Element): void {
        toggle.addEventListener('click', (e: Event) => {
            e.preventDefault();
            e.stopPropagation();

            const isOpen = menu.classList.contains('show');

            // Fermer les autres sous-menus du même niveau
            const parent = dropdown.closest('.custom-dropdown-menu');
            if (parent) {
                parent.querySelectorAll('.custom-dropstart .custom-dropdown-menu.show').forEach(m => {
                    if (m !== menu) {
                        m.classList.remove('show');
                        const t = m.previousElementSibling;
                        if (t) t.classList.remove('active');
                    }
                });
            }

            if (!isOpen) {
                menu.classList.add('show');
                toggle.classList.add('active');
            } else {
                menu.classList.remove('show');
                toggle.classList.remove('active');
            }
        });
    }

    private openDropdown(dropdown: Element, toggle: Element, menu: Element): void {
        menu.classList.add('show');
        toggle.classList.add('active');
        this.activeDropdown = dropdown;

        // Positionner le menu si nécessaire
        this.positionMenu(menu, toggle);
    }

    private closeAllDropdowns(): void {
        document.querySelectorAll('.custom-dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });

        document.querySelectorAll('.custom-dropdown-toggle.active').forEach(toggle => {
            toggle.classList.remove('active');
        });

        this.activeDropdown = null;
    }

    private positionMenu(menu: Element, toggle: Element): void {
        const menuElement = menu as HTMLElement;
        const toggleElement = toggle as HTMLElement;

        // Vérifier si le menu dépasse de l'écran
        const rect = menuElement.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        // Ajuster horizontalement si nécessaire
        if (rect.right > viewportWidth) {
            menuElement.style.left = 'auto';
            menuElement.style.right = '0';
        }

        // Ajuster verticalement si nécessaire
        if (rect.bottom > viewportHeight) {
            menuElement.style.top = 'auto';
            menuElement.style.bottom = '100%';
        }
    }

    /**
     * Méthode publique pour fermer tous les dropdowns
     */
    public closeAll(): void {
        this.closeAllDropdowns();
    }

    /**
     * Méthode publique pour ouvrir un dropdown spécifique
     */
    public open(selector: string): void {
        const dropdown = document.querySelector(selector);
        if (dropdown) {
            const toggle = dropdown.querySelector('.custom-dropdown-toggle');
            const menu = dropdown.querySelector('.custom-dropdown-menu');
            if (toggle && menu) {
                this.closeAllDropdowns();
                this.openDropdown(dropdown, toggle, menu);
            }
        }
    }
}
