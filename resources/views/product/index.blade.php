<x-layout> 
    <div class="w-full px-5 pb-20 max-w-screen-xl m-auto">
        
        {{-- BREADCRUMBS --}}
        <x-nav.breadcrumbs :$category />

        <h1 class="font-semibold text-3xl text-center mt-16 uppercase text-gray-700">{{ $category->title }}</h1>
        <p class="text-center mt-6 text-gray-500">{{ $category->description}}</p>
        <div class="products-container mt-14">

            {{-- FILTERS --}}
            <div class="flex justify-start items-center">
                <button class="peer border focus:border-red-400 bg-gray-50 ">
                    SORT BY
                </button>
                <div class="peer-focus:block hidden">
                    <option class="border focus:border-red-400">Asc</option>
                    <option>Desc</option>
                </div>
            </div>

            {{-- PRODUCTS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                @if ($products->isNotEmpty())            
                    @foreach ($products as $product)
                        <x-product.teaser :product="$product" />
                    @endforeach
                @else
                    <p class="text-center w-full col-span-full mt-20 text-lg font-semibold">No products found for '{{ $category->title }}' category.</p>
                @endif
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
        // Use event delegation on the parent container
        const productsContainer = document.querySelector('.products-container');

        productsContainer.addEventListener('submit', function (event) {
            if (event.target.matches('.wishlist-form')) {
                event.preventDefault(); 
                const form = event.target;
                const actionUrl = form.action;
                const method = form.method;
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(actionUrl, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken, 
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    
                    document.getElementById('wishlist-count').textContent = data.updatedWishlistCount;

                    const productForm = document.querySelector(`[data-product-id="${data.productId}"]`);
                    productForm.outerHTML = data.formHtml; 
                    if(data.status === 'added') {                                            
                        const wishlistIcon = document.querySelector('.wishlist-icon');

                        wishlistIcon.classList.add('animate');

                        setTimeout(() => {
                            wishlistIcon.classList.remove('animate');
                        }, 300); 
                    };
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });
</script>