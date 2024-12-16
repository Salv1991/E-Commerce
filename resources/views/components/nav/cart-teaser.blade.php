<li id="product-{{$product->id}}" data-teaser-{{$product->id}} class="grid grid-cols-4 gap-3">
    <a href="{{route('product', $product)}}" class="col-span-1 lg:col-span-1 w-full h-full overflow-hidden aspect-[.75]">
        <img 
            class="h-full w-full object-cover object-center" 
            src="{{ asset('storage/' . ($product->images->isNotEmpty() ? $product->images->first()->image_path : 'products/placeholder.jpg') )}}" 
            alt="Product Image">  
    </a>
    <div class="col-span-2 flex flex-col justify-between items-start">
        <div>
            <a href="{{ route('product', $product) }}" 
                class="quantity-title font-bold text-gray-600 hover:text-primary-500">
                {{$quantity . ' x ' . $product->title}}
            </a>
            <p class="line-clamp-2 text-xs text-gray-500">{{$product->description}}</p>
        </div>
        <span class="total mt-3 inline-block font-semibold text-sm">{{ number_format($quantity * $product->current_price, 2) }}$</span>                                           
    </div>
    <div class="col-span-1 justify-self-end">
        <form class="w-full" method='post' data-action="submit->cart#delete" action="{{ route('cart.delete', $product->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit">
                <x-heroicon-o-x-mark class="inline-block w-6 h-6 text-gray-500 hover:text-primary-500"/>
            </button>
        </form>
    </div>
</li>