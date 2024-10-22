<x-layout> 
    <div class="w-full px-5 pb-20 max-w-screen-xl m-auto">
        
        {{-- BREADCRUMBS --}}
        <x-nav.breadcrumbs :$category />

        {{-- PRODUCTS --}}
        <div class="wishlist-container">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
            @foreach ($products as $product)
                <x-product.teaser :product="$product" />
            @endforeach
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-20">
            {{ $products->links() }}
        </div>
    </div>
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Use event delegation on the document or a parent container
        const container = document.querySelector('.wishlist-container'); // Use the parent wrapper of the forms

        container.addEventListener('submit', function (event) {
            if (event.target.matches('.wishlist-form')) {
                event.preventDefault(); // Prevent default form submission

                const form = event.target;
                const actionUrl = form.action;
                const method = form.method;
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(actionUrl, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,  // Include CSRF token
                        'X-Requested-With': 'XMLHttpRequest' // Set header to indicate AJAX request
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data)

                    // Update the wishlist count
                    const wishlistCounterOnHeader = document.getElementById('wishlist-count'); 
                    wishlistCounterOnHeader.textContent = data.newCount; // Update count

                    // Replace the current form with the updated form HTML from the response
                    const productForm = document.querySelector(`[data-product-id="${data.productId}"]`);
                    productForm.outerHTML = data.formHtml; // Replace form

                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });
</script>