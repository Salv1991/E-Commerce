import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ['menu', 'close', 'firstDepth', 'categoriesContainer', 'categoriesMenuButton', 'categoryChildrenContainer', 'categoriesContainer2']

    connect() {
        this.menuIsOpen = false;
        this.currentlySelectedCategoryId = '';
        this.bindEvents();      
    }

    bindEvents() {
        document.addEventListener('click', ()=> {
            this.menuIsOpen = false;
            document.getElementById('categories-container').classList.toggle('hidden', !this.menuIsOpen);
            document.querySelector('.open').classList.toggle('hidden', this.menuIsOpen);
            document.querySelector('.close').classList.toggle('hidden', !this.menuIsOpen); 
        });
        
        document.getElementById('categories-container').addEventListener('click', (event) => {
            event.stopPropagation();
        })
        document.getElementById('categories-wrapper').addEventListener('click', (event) => {
            event.stopPropagation();
        })
    }

    toggleResponsiveMenu() {
        this.menuTarget.classList.toggle('translate-x-0');
        this.menuTarget.classList.toggle('translate-x-full');
    }

    openChildrenCategoriesMenu(event) {
        const selectedCategoryId = event.currentTarget.dataset.categoryId;

        if(selectedCategoryId !== this.currentlySelectedCategoryId) {

            this.currentlySelectedCategoryId = selectedCategoryId;

            this.categoryChildrenContainerTarget.classList.add('bg-white', 'border', 'border-stone-300', 'border-l-0');

            this.firstDepthTargets.forEach( (target) => {
                target.classList.remove('bg-gradient-to-br', 'from-red-500', 'to-pink-500', 'text-white');
            });
            
            event.currentTarget.classList.add('bg-gradient-to-br', 'from-red-500', 'to-pink-500', 'text-white');

            const containersToShow = this.categoriesContainerTargets.filter((container) => {
                container.classList.toggle('block', selectedCategoryId == container.dataset.parentId);
                container.classList.toggle('hidden', selectedCategoryId != container.dataset.parentId);
            });        
        }
    }

    toggleCategoriesMenu() {
        this.menuIsOpen = !this.menuIsOpen;
        document.getElementById('categories-container').classList.toggle('hidden');
        document.querySelector('.open').classList.toggle('hidden');
        document.querySelector('.close').classList.toggle('hidden');

    }
}