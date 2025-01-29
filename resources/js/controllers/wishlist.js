import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['form', 'icon', 'productsContainer', 'wishlistsContainer'];

    connect() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    }

    toggle(event) {
        event.preventDefault();
        const selectedWishlistForm = event.currentTarget;
        const wishlistButton = selectedWishlistForm.querySelector('button');
        wishlistButton.disabled = true;

        const wishlistIcon = selectedWishlistForm.querySelector('.wishlist-icon');
        const wishlistText = selectedWishlistForm.querySelector('.wishlist-text');
        const headerWishlistIcon = document.querySelector('header .wishlist-icon');
        const wasWishlisted = wishlistIcon.classList.contains('fill-red-400');

        if(!wasWishlisted){
            wishlistIcon.classList.add('animate', 'fill-red-400', 'text-white/10');
            wishlistIcon.classList.remove('fill-gray-300/70', 'text-transparent'); 
            headerWishlistIcon.classList.add('animate', 'fill-red-400','text-white/10');
            headerWishlistIcon.classList.remove('text-gray-700');

            if(wishlistText){
                wishlistText.textContent = 'Remove from Wishlist';
            }

            setTimeout(() => {
                headerWishlistIcon.classList.remove('animate', 'fill-red-400', 'text-white/10');
                headerWishlistIcon.classList.add('text-gray-700');
                wishlistIcon.classList.remove('animate');
            }, 300); 
        } else {
            wishlistIcon.classList.remove('fill-red-400', 'text-white/10'); 
            wishlistIcon.classList.add('fill-gray-300/70', 'text-transparent'); 
            
            if(wishlistText){
                wishlistText.textContent = 'Add to Wishlist';
            }
        }
        
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
        })
        .finally(() => {
            wishlistButton.disabled = false;
        })
        .catch(error => {
            console.log('Error:', error);
            if (wasWishlisted) {
                wishlistIcon.classList.add('fill-red-400', 'text-white/10');
                wishlistIcon.classList.remove('fill-gray-300/70', 'text-transparent');
                if (wishlistText) {
                    wishlistText.textContent = 'Remove from Wishlist';
                }
            } else {
                wishlistIcon.classList.remove('fill-red-400', 'text-white/10');
                wishlistIcon.classList.add('fill-gray-300/70', 'text-transparent');
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
