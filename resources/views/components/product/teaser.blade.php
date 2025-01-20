@props(['product', 'isWishlisted' => false])
<a href="{{ route('product', $product) }}" class="col-span-1 relative" >

    {{-- IMAGE CONTAINER --}}
    <div class="aspect-[350/416] relative">
        <img 
            src="{{ asset('storage/' . ($product->images->isNotEmpty() ? $product->images->first()->image_path : 'products/placeholder.jpg') )}}" 
            alt="Product Image" 
            class="w-full h-full object-cover rounded-md" />
            
            @if ($product->stock <= 0)                
                <div class="flex justify-center items-center absolute top-0 bottom-0 right-0 left-0 bg-black/50">
                    <span class="text-white">Out of stock</span>
                </div>
            @endif

        {{-- WISHLIST BUTTON --}}
        @auth   
        <div class="absolute top-3 right-3">
            <x-form.wishlist-toggle :product='$product' :$isWishlisted/>
        </div>
        @endauth
    </div>

    {{-- PRODUCT DETAILS --}}
    <div class="flex justify-between items-center mt-3">
        <div>
            <h2 class="text-lg font-bold text-start">{{$product->title}}</h2>
            <x-product.price :$product />
        </div>
        <div class="*:inline-block">
           
            {{-- ADD TO CART BUTTON --}}
            <x-form.add-to-cart :$product />
        </div>
    </div>
</a>
