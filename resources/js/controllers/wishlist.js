import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ["form", "icon"];

    submit(event) {
        event.preventDefault(); // Prevent default form submission
        const form = event.currentTarget;

        // Use the appropriate method based on the action
        const method = form.method; // This should be either POST or DELETE

        fetch(form.action, {
            method: method,
            body: method === "POST" ? new FormData(form) : null, // Only add body for POST requests
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === "added") {
                this.iconTarget.classList.add('fill-red-300', 'text-red-300');
                this.iconTarget.classList.remove('hover:fill-red-300', 'hover:text-red-300');

                // Update form action and method for removing from wishlist
                this.updateFormForRemoving(data.productId);
            } else if (data.status === "removed") {
                this.iconTarget.classList.remove('fill-red-300', 'text-red-300');
                this.iconTarget.classList.add('hover:fill-red-300', 'hover:text-red-300');

                // Update form action and method for adding to wishlist
                this.updateFormForAdding(data.productId);
            }

            // Update wishlist count
            const wishlistCountElement = document.getElementById("wishlist-count");
            if (wishlistCountElement) {
                wishlistCountElement.textContent = data.newCount; // Assuming newCount is returned in the response
            }
        })
        .catch(error => console.error('Error:', error));
    }

    updateFormForRemoving(productId) {
        this.formTarget.action = `/wishlist/${productId}`; // Adjust to your route for removing
        this.formTarget.method = 'DELETE'; // Change method to DELETE
    }

    updateFormForAdding(productId) {
        this.formTarget.action = `/wishlist/${productId}`; // Adjust to your route for adding
        this.formTarget.method = 'POST'; // Change method back to POST
    }
}
