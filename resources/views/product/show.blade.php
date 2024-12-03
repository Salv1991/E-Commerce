<x-layout>
    <div 
        class="w-full px-5 pb-20 max-w-screen-xl m-auto" 
        data-controller="wishlist" 
        data-filter-target="productsContainer" >
        
        {{-- BREADCRUMBS --}}
        <x-nav.breadcrumbs :category="$product->categories->first()" :$product />

        <div class="w-full m-auto grid grid-cols-2 gap-5 *:col-span-full *:md:col-span-1">

            {{-- IMAGE --}}
            <div class="aspect-[300/416] ">
                @if ($product->images->isNotEmpty())
                <img 
                    src="{{ asset('storage/' . $product->image->image_path) }}" 
                    alt="Product Image" 
                    class="w-full h-full object-cover" />
                @else
                    <img 
                        src="{{ asset('storage/products/placeholder.jpg') }}" 
                        alt="Product Image" 
                        class="w-full h-full object-cover" />
                @endif
            </div>

            {{-- DETAILS --}}
            <div>
                <div class="">
                    <h1 class="text-5xl font-bold">{{$product->title}}</h1>
                    <!-- <div class="mt-5">Reviews</div> -->
                    <div class="flex  justify-start items-center gap-2 mt-2">
                        <x-product.price class="*:text-xl" :$product />
                    </div>
                    <div class="mt-5">
                        <p>Stock: {{ $product->stock }}</p>
                        <p>Brand: {{ $product->stock }}</p>
                        <p>Manufactured: {{ $product->stock }}</p>
                    </div>
                    <div class="mt-5">
                        <p>{{ $product->description }}</p>
                    </div>
                    <div class="mt-10 flex flex-col xl:flex-row justify-center items-center gap-5 *:text-2xl *:rounded-sm *:w-full">

                        {{-- CART --}}
                        <form action="{{ route('cart.add', $product->id) }}" method="post">
                            @csrf
                            <button type="submit" 
                                class="w-full px-4 py-4 bg-black border-2 border-black hover:bg-white hover:text-black 
                                text-white duration-300">
                                <span>Add to cart</span> 
                                <x-heroicon-c-shopping-bag class="inline-block w-7 h-7 -translate-y-1"/>
                            </button>
                        </form>

                        {{-- WISHLIST --}}
                        @auth
                            <x-form.wishlist-toggle-alternative 
                                :product='$product' 
                                :isWishlisted="$wishlistedProductsIds->contains($product->id)"/>      
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>