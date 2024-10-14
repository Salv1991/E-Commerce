<a href={{route('product', $product)}} class="col-span-1">
    <div class="aspect-[278/416]">
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

    <div>
        <div>
            <h2 class="text-lg font-bold text-center">{{$product->title}}</h2>
            <div class="m-auto w-fit *:inline-block">
                <p @class([
                    'text-base font-semibold text-gray-400',
                    'line-through' => $product->discounted_price,
                    ])>${{$product->price}}</p>

                @if ($product->discounted_price)
                    <p class="text-base font-bold text-primary-500">${{$product->discounted_price}}</p>   
                @endif
            </div>
        </div>
    </div>
    <!-- <button class="px-4 py-2 bg-gray-800 text-white rounded-sm">Add to cart</button> -->
</a>