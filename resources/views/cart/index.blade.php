<x-layout>
    <div class="mt-16 max-w-screen-xl m-auto p-6">

        <h1 class="text-center text-4xl font-semibold">My Cart</h1>

        <div id="cart-container" class="mt-14 grid grid-cols-4 items-start gap-8 bg-gray-100 p-2 ">
            <div data-controller="wishlist" data-filter-target="productsContainer" class="col-span-3 bg-white px-5 divide-y-2">
                @if ($cart->isNotEmpty())
                    @foreach ($cart as $lineItem )
                        <div class="grid grid-cols-7 py-5 gap-4">
                            <a href="{{route('product', $lineItem->product)}}" 
                                class="w-full h-full overflow-hidden aspect-square col-span-1">
                                <img 
                                    class="h-full w-full object-cover object-center" 
                                    src="{{ asset('storage/' . ($lineItem->product->images->isNotEmpty() ? $lineItem->product->images->first()->image_path : 'products/placeholder.jpg') )}}" 
                                    alt="Product Image">  
                            </a>
                            <div class="col-span-2">
                                <a href="{{route('product', $lineItem->product)}}" class="text-xl font-bold hover:text-primary-500">{{ $lineItem->product->title }}</a>
                            </div>
                            <div class="col-span-4 grid grid-cols-2">
                                <div class="flex justify-center items-center gap-3">
                                    <div class="border border-gray-300 rounded-md py-1 px-2 flex justify-start items-center gap-2">
                                        <div class="text-xl">{{$lineItem->quantity}}</div>
                                        <div><x-heroicon-o-chevron-down class="inline-block w-4 h-4"/></div>
                                    </div>
                                    <x-form.wishlist-toggle :product='$lineItem->product' :isWishlisted="in_array($lineItem->product->id, $wishlistedProductsIds)"/>
                                    <div>
                                        <form class="w-full" method='post' action="{{ route('cart.delete', $lineItem->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">
                                                <x-heroicon-o-trash class="inline-block w-7 h-7 text-gray-500"/>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="flex justify-center items-center gap-2">
                                    <x-product.price :product="$lineItem->product" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <span> Your Cart is empty. </span>
                @endif
            </div>
            <div class="col-span-1 bg-white p-5">
                <div class="flex flex-col justify-center items-start gap-2 *:w-full">
                    <h2 class="text-lg font-bold">Order Summary</h2>
                    
                    <div class="flex justify-between items-center">
                        <span>Subtotal (VAT included):</span>
                        <span>123,40$</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Shipping Fee:</span>
                        <span>3,40$</span>
                    </div>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-lg font-bold">Total:</span>
                        <span>223,40$</span>
                    </div>
                    <button class="w-full mt-2 px-4 py-4 bg-black border-2 border-black hover:bg-white hover:text-black  text-white duration-300">
                        <span class="text-center">Proceed to Checkout</span> 
                    </button>                
                </div>
            </div>
        </div>
    </div>
</x-layout>