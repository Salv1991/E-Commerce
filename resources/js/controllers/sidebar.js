import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['sidebar', 'content', 'sidebarSmall', 'button'];

    connect() {
        this.buttonTargets.forEach(element => {
            console.log(element.dataset);
        });
    }

    toggle() {
        this.sidebarTarget.classList.toggle('-translate-x-full');
        this.sidebarSmallTarget.classList.toggle('-translate-x-full');
        this.contentTarget.classList.toggle('ml-64');    
        this.contentTarget.classList.toggle('ml-16');    
    }

    openSubmenu(event) {
        const parentContainer = event.currentTarget.closest('li');
        const submenu = parentContainer.querySelector('ul[data-submenu]');
    
        parentContainer.querySelector('[data-open]').classList.toggle('hidden');
        parentContainer.querySelector('[data-close]').classList.toggle('hidden');
        
        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden'); 
            setTimeout(() => submenu.classList.remove('scale-y-0'), 1);
        } else {
            submenu.classList.add('scale-y-0');
           submenu.classList.add('hidden');
        }
    }
}
