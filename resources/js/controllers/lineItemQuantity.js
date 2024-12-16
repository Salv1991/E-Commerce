import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ['quantityButton', 'closed', 'open'];

    connect() {
        document.addEventListener('click', this.handleClickOutside.bind(this));
    }

    disconnect() {
        document.removeEventListener('click', this.handleClickOutside.bind(this));
    }

    openMenu(event){
       event.currentTarget.closest('.quantity-container').querySelector('.quantity-menu').classList.toggle('hidden');
       event.currentTarget.closest('.quantity-container').querySelector('.closed-chevron').classList.toggle('hidden');
       event.currentTarget.closest('.quantity-container').querySelector('.open-chevron').classList.toggle('hidden');
    }
    
    handleClickOutside(event){
        const quantityContainers = document.querySelectorAll('.quantity-container');
        quantityContainers.forEach(container => {
            const menu = container.querySelector('.quantity-menu');

            if( !container.contains(event.target) && !menu.classList.contains('hidden') ){
                menu.classList.add('hidden');
                container.querySelector('.open-chevron').classList.toggle('hidden');
                container.querySelector('.closed-chevron').classList.toggle('hidden');
            }
        })

    }
}