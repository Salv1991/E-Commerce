import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ['firstDepth','submenu', 'menu', 'close', 'responsiveMenuContainer', 'closeButton']

    connect() {
        this.selectedCategory;
        document.querySelectorAll('.category-container').forEach(container => {
            container.addEventListener('mouseover', event => event.stopPropagation());
        });
        console.log( document.querySelector('div[data-responsive-nav-menu-target="responsiveMenuContainer"]'));
        document.querySelector('div[data-responsive-nav-menu-target="responsiveMenuContainer"]').addEventListener('click', this.handleClickOutside.bind(this)); 
    }

    disconnect() {
        document.removeEventListener('click', this.handleClickOutside.bind(this));
    }

    handleClickOutside(event){
        if(!document.querySelector('div[data-responsive-nav-menu-target="menu"]').contains(event.target)){
            this.toggleResponsiveMenu();
        }
    }

    toggleResponsiveMenu() {
        this.responsiveMenuContainerTarget.classList.toggle('opacity-100');
        this.responsiveMenuContainerTarget.classList.toggle('pointer-events-none');
        this.closeButtonTarget.classList.toggle('hidden');
        this.menuTarget.classList.toggle('-translate-x-full');
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
    
    toggleMobileSubmenu(event) {
        console.log(event.currentTarget);
        event.currentTarget.closest('.mobile-category').querySelector('.mobile-category-submenu').classList.toggle('-translate-x-full');
    }

    toggleMobileChildrenSubmenu(event) {
        console.log('EVENT MOBILE', event.currentTarget);
        console.log('MOBILE CHILDREN',event.currentTarget.closest('.mobile-submenu'));
        event.currentTarget.closest('.children-mobile-submenu').querySelector('.submenu-card').classList.toggle('-translate-x-full');
    }
}