import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ['form', 'icon', 'productsContainer', 'wishlistsContainer'];

    connect() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    }

    toggle(event) {
        event.preventDefault();
        const selectedWishlistForm = event.currentTarget;
        const formData = new FormData(selectedWishlistForm);
        const viewType = selectedWishlistForm.getAttribute('data-view-type');
        const wishlistButton = selectedWishlistForm.querySelector('button');
        const wishlistIcon = selectedWishlistForm.querySelector('.wishlist-icon');
        const wishlistText = selectedWishlistForm.querySelector('.wishlist-text');

        wishlistButton.disabled = true;
        
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

            if(data.status === 'added'){
                const headerWishlistIcon = document.querySelector('header .wishlist-icon');
                
                if(wishlistText){
                    wishlistText.textContent = 'Remove from Wishlist';
                }

                headerWishlistIcon.classList.add('animate');
                wishlistIcon.classList.add('animate', 'fill-red-300', 'text-red-300');
            
                setTimeout(() => {
                    headerWishlistIcon.classList.remove('animate');
                    wishlistIcon.classList.remove('animate');
                }, 300); 
            } else {
                wishlistIcon.classList.remove('fill-red-300', 'text-red-300'); 
                
                if(wishlistText){
                    wishlistText.textContent = 'Add to Wishlist';
                }
            }
        })
        .finally(() => {
            wishlistButton.disabled = false;

        })
        .catch(error => {
            console.log('Error:', error);
            if (data.status === 'added') {
                wishlistIcon.classList.remove('fill-red-300', 'text-red-300');
                if (wishlistText) {
                    wishlistText.textContent = 'Add to Wishlist';
                }
            }
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
