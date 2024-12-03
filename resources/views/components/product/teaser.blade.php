@props(['product', 'isWishlisted' => false])
<a href="{{ route('product', $product) }}" class="col-span-1 relative" >

    {{-- IMAGE CONTAINER --}}
    <div class="aspect-[308/416] relative">
        <img 
            src="{{ asset('storage/' . ($product->images->isNotEmpty() ? $product->images->first()->image_path : 'products/placeholder.jpg') )}}" 
            alt="Product Image" 
            class="w-full h-full object-cover rounded-md" />
        {{-- WISHLIST BUTTON --}}
        @auth   
        <div class="absolute top-5 lg:top-3 right-7 lg:right-3">
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
