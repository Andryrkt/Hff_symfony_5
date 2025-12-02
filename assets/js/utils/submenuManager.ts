// assets/js/utils/submenuManager.ts

export class SubmenuManager {
    constructor() {}

    public init(): void {
        document.addEventListener('click', (event) => {
            const target = event.target as HTMLElement;
            const dropdownToggle = target.closest('.dropdown-toggle');

            if (dropdownToggle) {
                const parentLi = dropdownToggle.closest('li');
                if (!parentLi) return;

                const isSubmenuToggle = parentLi.classList.contains('dropend') || parentLi.classList.contains('dropstart');
                const dropdownMenu = dropdownToggle.nextElementSibling as HTMLElement;

                if (isSubmenuToggle && dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Fermer les autres sous-menus ouverts au même niveau
                    const parentMenu = parentLi.closest('ul.dropdown-menu');
                    if (parentMenu) {
                        const openSubmenus = parentMenu.querySelectorAll(':scope > .dropend > .dropdown-menu.show');
                        openSubmenus.forEach((submenu) => {
                            if (submenu !== dropdownMenu) {
                                submenu.classList.remove('show');
                            }
                        });
                    }
                    
                    dropdownMenu.classList.toggle('show');
                }
            } else {
                // Fermer tous les sous-menus si on clique en dehors
                const openSubmenus = document.querySelectorAll('.dropdown-menu .dropdown-menu.show');
                openSubmenus.forEach((submenu) => {
                    submenu.classList.remove('show');
                });

                // Cela ne fermera pas le menu principal, bootstrap s'en occupe
            }
        });
    }
}
