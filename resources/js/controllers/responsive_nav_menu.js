import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ['firstDepth','submenu', 'menu', 'close']

    connect() {
        this.selectedCategory;
        document.querySelectorAll('.category-container').forEach(container => {
            container.addEventListener('mouseover', event => event.stopPropagation());
        });
    }

    toggleResponsiveMenu() {
        this.menuTarget.classList.toggle('translate-x-0');
        this.menuTarget.classList.toggle('translate-x-full');
    }

    openSubmenu(event) {
        event.stopPropagation();
        this.submenuTargets.forEach( submenu =>  submenu.classList.add('hidden'));
        const selectedCategory = event.currentTarget;
        selectedCategory.querySelector('.submenu').classList.toggle('hidden');
    }

    closeSubmenu() {
        this.submenuTargets.forEach( submenu => {
            submenu.classList.add('hidden');
        }) 
    }

    closeSubmenu2(event) {
        event.stopPropagation();
        event.currentTarget.closest('div.submenu').classList.add('hidden');         
    }
}