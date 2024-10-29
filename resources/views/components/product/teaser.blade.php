<a href="{{ route('product', $product) }}" class="col-span-1 relative" >
    {{-- IMAGE CONTAINER --}}
    <div class="aspect-[308/416]">
        @if ($product->images?->isNotEmpty())
            <img 
                src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                alt="Product Image" 
                class="w-full h-full object-cover" />
        @else
            <img 
                src="{{ asset('storage/products/placeholder.jpg') }}" 
                alt="Product Image" 
                class="w-full h-full object-cover" />
        @endif
    </div>

    {{-- PRODUCT DETAILS --}}
    <div class="flex justify-between items-center mt-3">
        <div>
            <h2 class="text-lg font-bold text-start">{{$product->title}}</h2>

            <div class="w-fit *:inline-block">
                <p @class([
                        'text-base font-semibold text-gray-400',
                        'line-through' => $product->discounted_price,
                    ])>
                    ${{$product->price}}
                </p>

                @if ($product->discounted_price)
                    <p class="text-base font-bold text-primary-500">${{$product->discounted_price}}</p>   
                @endif
            </div>
        </div>

        {{-- WISHLIST BUTTON --}}
        @auth   
            <x-form.wishlist-toggle :product='$product' :isWishlisted="$product->isWishlistedByUser()"/>
        @endauth
        
    </div>
</a>
