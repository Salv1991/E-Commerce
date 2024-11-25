import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ["form", "icon", 'productsContainer', 'wishlistsContainer'];

    connect() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    }

    toggle(event) {
        event.preventDefault();
        const selectedWishlistForm = event.currentTarget;
        const formData = new FormData(selectedWishlistForm);
        const viewType = selectedWishlistForm.getAttribute('data-view-type');
        selectedWishlistForm.querySelector('button').disabled = true;
        formData.append('viewType', viewType);

        fetch( selectedWishlistForm.action, {
            method: selectedWishlistForm.method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('wishlist-count').textContent = data.updatedWishlistCount;
            selectedWishlistForm.outerHTML = data.formHtml;
            if(data.status === 'added'){
                const wishlistIcon = document.querySelector('.wishlist-icon');

                wishlistIcon.classList.add('animate');

                setTimeout(() => {
                    wishlistIcon.classList.remove('animate');
                }, 300); 
            }
        })
        .catch(error => {
            console.log('Error:', error);
        });
    }

    remove(event) {
        event.preventDefault();
        const selectedWishlistForm = event.currentTarget;
        selectedWishlistForm.querySelector('button').disabled = true;

        fetch(selectedWishlistForm.action, {
            method: selectedWishlistForm.method,
            body: new FormData(selectedWishlistForm),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.csrfToken,
            }
        })
        .then(response => response.json())
        .then(data => {
            selectedWishlistForm.closest(`#wishlist-item-${data.productId}`).remove();
            document.getElementById('wishlist-count').textContent = data.updatedWishlistCount;
        })
    }
}
