<h2 class="text-xl font-semibold border-b border-black pb-2">
    Your order 
    <span class="text-sm text-gray-500">( {{ $cartCount }} )</span>
</h2> 
<div class="max-h-[670px] overflow-y-auto my-2 divide-y-2 pr-2">
    @if ( $cart->isNotEmpty())
        @foreach ( $cart as $lineItem )
            <div id="product-{{$lineItem->product->id}}" 
                data-teaser-{{$lineItem->product->id}} 
                class="bg-white py-5 gap-2 flex justify-between items-center">
                
                <a href="{{route('product', $lineItem->product)}}" 
                    class="h-32 overflow-hidden aspect-[.75] col-span-2 lg:col-span-1">
                    <img 
                        class="h-full w-full object-cover object-center" 
                        src="{{ asset('storage/' . ($lineItem->product->images->isNotEmpty() 
                            ? $lineItem->product->images->first()->image_path 
                            : 'products/placeholder.jpg') )}}" 
                        alt="Product Image">  
                </a>

                <div class="flex-1 flex flex-col justify-between gap-4">
                    <div class="flex flex-col items-start xs:items-center xs:flex-row md:items-start md:flex-col lg:flex-row lg:items-center gap-3"> 
                        
                        {{-- TITLE --}}
                        <div class="flex-1">
                            <a  href="{{route('product', $lineItem->product)}}" 
                                class="text-xl font-bold hover:text-primary-500">
                                {{ $lineItem->product->title }}
                            </a>
                            <div class="flex flex-col justify-start items-start mt-2 *:text-sm *:text-gray-500">
                                <span>Quantity: {{ $lineItem->quantity }}</span>        
                                <span>Stock: {{ $lineItem->product->stock }}</span>        
                                <span>MPN: {{ $lineItem->product->mpn }}</span>
                            </div>
                        </div>

                        {{-- PRICE --}}
                            <x-product.price :product="$lineItem->product" />
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div> 
<div class="{{ $cart->isNotEmpty() ? 'hidden' : 'flex'}} empty-cart-message bg-white h-96 px-5 py-5 flex justify-center items-center text-gray-500">
    Your Cart is empty.
</div>