<div 
    id="wishlist-item-{{ $product->id }}"
    data-product-teaser="{{$product->id}}" 
    class="grid grid-cols-8 items-center justify-center gap-5 h-auto md:h-auto w-full p-4 bg-gray-50 relative">

    <div class="grid grid-cols-2 col-span-3 lg:col-span-2 h-full">
        <form 
            id="wishlist-form-{{ $product->id }}" 
            action="{{ route('wishlist.create', $product->id) }}" 
            method="post" 
            class="wishlist-form col-span-1 m-auto absolute top-6 right-6 lg:block lg:static">
            @csrf
            <button type="submit">
                <x-heroicon-o-x-mark class="w-6 h-6 text-gray-500 hover:text-primary-500 "/>
            </button>
        </form>

        <a href="{{route('product', $product)}}" class="col-span-2 lg:col-span-1 w-full h-full overflow-hidden aspect-square">
            <img 
                class="h-full w-full object-cover object-center" 
                src="{{ asset('storage/' . ($product->images->isNotEmpty() ? $product->images->first()->image_path : 'products/placeholder.jpg') )}}" 
                alt="Product Image">  
        </a>
    </div>

    <div class="grid grid-rows-4 lg:grid-rows-1 grid-cols-1 lg:grid-cols-6 col-span-5 lg:col-span-6 items-center">
        <div class="col-span-1 lg:col-span-2 font-semibold text-lg">
            <a href="{{route('product', $product)}}" class="hover:text-primary-500">{{ $product->title }}</a>
        </div>

        <div class="col-span-1 font-semibold text-gray-600">
            <x-product.price :$product />
        </div>

        <div class="col-span-1 font-semibold text-green-500">
            <span>In stock</span>
        </div>

        <div class="col-span-1 lg:col-span-2 lg:m-auto">
            <button class="px-6 py-3 bg-black border-2 border-black hover:bg-white hover:text-black  text-white duration-300">
                Add to cart 
                <x-heroicon-c-shopping-bag class="inline-block w-5 h-5 -translate-y-1 ml-2"/>
            </button>
        </div>
    </div>

</div>