<div 
    id="wishlist-item-{{ $product->id }}"
    data-product-teaser="{{$product->id}}" 
    class="grid grid-cols-8 items-center justify-center gap-5 h-auto md:h-auto w-full p-4 bg-gray-50 relative">

    <div class="grid grid-cols-2 col-span-3 lg:col-span-2 h-full">
        <form 
            id="wishlist-form-{{ $product->id }}" 
            action="{{ route('wishlist.toggle', $product->id) }}" 
            data-action="submit->wishlist#remove"
            method="post" 
            class="wishlist-form col-span-1 m-auto absolute top-6 right-6 lg:block lg:static">
            @csrf
            <button type="submit">
                <x-heroicon-o-x-mark class="w-6 h-6 text-gray-500 hover:text-primary-500 "/>
            </button>
        </form>

        <a href="{{route('product', $product)}}" 
            class="w-full h-full overflow-hidden aspect-[.9]  lg:aspect-[.75] col-span-2 lg:col-span-1">
            <img 
                class="h-full w-full object-cover object-center rounded-sm" 
                src="{{ asset('storage/' . ($product->images->isNotEmpty() 
                    ? $product->images->first()->image_path 
                    : 'products/placeholder.jpg') )}}" 
                alt="Product Image">  
        </a>
    </div>

    <div class="h-full lg:grid grid-cols-1 lg:grid-cols-7 col-span-5 lg:col-span-6 lg:items-center flex flex-col justify-between items-start">
        <div class="grid grid-cols-5 col-span-5 space-y-2">
            <div class="col-span-2 font-semibold text-lg">
                <a href="{{route('product', $product)}}" class="hover:text-primary-500">{{ $product->title }}</a>
            </div>

            <div class="col-span-full lg:col-span-2 font-semibold text-gray-600">
                <x-product.price :$product />
            </div>

            <div class="col-span-full lg:col-span-1 font-semibold">
                @if ($product->stock > 0)
                    <span class="text-green-500">In stock</span>
                @else
                    <span class="text-red-500">Out of stock</span>
                @endif
            </div>
        </div>

        <div class="col-span-full lg:col-span-2 lg:m-auto">
            <form action="{{ route('cart.add', $product) }}" data-action="submit->cart#add" method="post">
                @csrf
                <button @disabled($product->stock <= 0) type="submit" class="px-4 py-3 bg-black border-2 border-black hover:bg-white hover:text-black disabled:bg-black/40 disabled:border-none disabled:hover:bg-black/40 text-white duration-300 disabled:text-white">
                    Add to cart 
                    <x-heroicon-c-shopping-bag class="inline-block w-5 h-5 -translate-y-1 ml-1"/>
                </button>
            </form>
        </div>
    </div>
</div>