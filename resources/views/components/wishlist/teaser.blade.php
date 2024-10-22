<div id="wishlist-item-{{ $product->id }}" class="grid grid-cols-8 items-center justify-center gap-5 h-40 w-full p-4 bg-gray-50">


    <form id="wishlist-form-{{ $product->id }}" action="{{ route('wishlist.destroy', $product->id) }}" method="post" class="col-span-1 m-auto">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="removeWishlistItem(event, {{ $product->id }})">
            <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500 hover:text-primary-500 "/>
        </button>
    </form>
   
    <div class="col-span-1 h-full overflow-hidden ">
        <img class="h-fit w-full object-cover object-center" src="{{ asset('storage/products/placeholder.jpg') }}" alt="Product Image">
    </div>

    <div class="col-span-2 font-semibold text-lg">
        <span>{{ $product->title }}</span>
    </div>

    <div class="col-span-1 font-semibold text-gray-600">
        <span>{{ $product->price }}</span>
    </div>

    <div class="col-span-1 font-semibold text-green-500">
        <span>In stock</span>
    </div>

    <div class="col-span-2 m-auto">
        <button class="px-6 py-3 bg-black border-2 border-black hover:bg-white hover:text-black  text-white duration-300">
            Add to cart 
            <x-heroicon-c-shopping-bag class="inline-block w-5 h-5 -translate-y-1 ml-2"/>
        </button>
    </div>
</div>

<script>
    function removeWishlistItem(event, productId) {
        event.preventDefault(); // Prevent the form from submitting traditionally

        const form = document.getElementById(`wishlist-form-${productId}`);

        fetch(form.action, {
            method: form.method,
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'removed') {
                // Remove the wishlist item from the DOM
                const wishlistItem = document.getElementById(`wishlist-item-${productId}`);
                if (wishlistItem) {
                    wishlistItem.remove();
                }

                // Update the wishlist count in the header if needed
                const wishlistCountElement = document.getElementById("wishlist-count");
                if (wishlistCountElement) {
                    wishlistCountElement.textContent = data.newCount;
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>