import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ['form', 'icon', 'productsContainer', 'wishlistsContainer'];

    connect() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    }

    toggle(event) {
        event.preventDefault();
        const selectedWishlistForm = event.currentTarget;
        const wishlistButton = selectedWishlistForm.querySelector('button');
        const wishlistIcon = selectedWishlistForm.querySelector('.wishlist-icon');
        const wishlistText = selectedWishlistForm.querySelector('.wishlist-text');

        wishlistButton.disabled = true;
        
        fetch( selectedWishlistForm.action, {
            method: selectedWishlistForm.method,
            body: new FormData(selectedWishlistForm),
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
                wishlistIcon.classList.remove('fill-gray-300/70', 'text-transparent'); 

                headerWishlistIcon.classList.add('animate');

                wishlistIcon.classList.add('animate', 'fill-red-400', 'text-white/10');
                
                if(wishlistText){
                    wishlistText.textContent = 'Remove from Wishlist';
                }
        
                setTimeout(() => {
                    headerWishlistIcon.classList.remove('animate');
                    wishlistIcon.classList.remove('animate');
                }, 300); 
            } else {
                wishlistIcon.classList.remove('fill-red-400', 'text-white/10'); 
                wishlistIcon.classList.add('fill-gray-300/70', 'text-transparent'); 
                
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
                wishlistIcon.classList.remove('fill-red-300', 'text-white/20');
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
            document.querySelector('.empty-wishlist-message').classList.toggle('hidden', data.updatedWishlistCount > 0);      
        })
    }
}
