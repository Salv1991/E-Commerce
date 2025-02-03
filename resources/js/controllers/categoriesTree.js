import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = [];

    connect() {
    }

    selectCategory(event) {
        event.preventDefault();
        if (!event.currentTarget.checked) {
            this.uncheckChildren(event.currentTarget);
        } else{
           this.checkParents(event.currentTarget);
        }
    }   

    checkParents(checkboxSelected) {
        const firstDepthInput = checkboxSelected.closest('div[data-firstDepthCategory]').querySelector('input');
        const secondDepthInput = checkboxSelected.closest('div[data-secondDepthCategory]')?.querySelector('input');

        firstDepthInput.setAttribute('checked', true);
        firstDepthInput.checked = true;
        
        if(secondDepthInput){
            secondDepthInput.setAttribute('checked', true);
            secondDepthInput.checked = true;
        }
    }

    uncheckChildren(selectedCheckbox) {
        const firstDepthCategoryContainer = selectedCheckbox.closest('div[data-firstDepthCategory]');
        const secondDepthCategoryContainer = selectedCheckbox.closest('div[data-secondDepthCategory]');

        if(secondDepthCategoryContainer) {
            secondDepthCategoryContainer.querySelector('div[data-thirdDepthCategory]').querySelectorAll('input').forEach(input => {
                input.checked = false; 
                    input.removeAttribute('checked'); 
            });
        } else if (firstDepthCategoryContainer) {
            firstDepthCategoryContainer.querySelectorAll('div[data-secondDepthCategory]').forEach((secondDepthCategory) => {
                secondDepthCategory.querySelectorAll('input').forEach(input => {
                    input.checked = false; 
                    input.removeAttribute('checked');  
                });
            });
        }
    }
}
