<x-layout>
    <div class="mt-16 max-w-screen-xl m-auto p-6">

        <h1 class="text-center text-4xl font-semibold">Cart</h1>

        <div class="mt-14 grid grid-cols-6 items-start gap-8 bg-gray-100">
            <div 
                data-controller="wishlist cart lineItemQuantity" 
                data-filter-target="productsContainer" 
                class="cart-teasers-container col-span-full lg:col-span-4 divide-y-2 bg-white px-5">

                @if ($cart->isNotEmpty())
                    @foreach ($cart as $lineItem )
                        <div id="product-{{$lineItem->product->id}}" 
                            data-teaser-{{$lineItem->product->id}} 
                            class="bg-white grid grid-cols-7 py-5 gap-4">
                          
                            <a href="{{route('product', $lineItem->product)}}" 
                                class="w-full h-full overflow-hidden aspect-[.75] col-span-2 lg:col-span-1">
                                <img 
                                    class="h-full w-full object-cover object-center" 
                                    src="{{ asset('storage/' . ($lineItem->product->images->isNotEmpty() 
                                        ? $lineItem->product->images->first()->image_path 
                                        : 'products/placeholder.jpg') )}}" 
                                    alt="Product Image">  
                            </a>

                            <div class="col-span-5 lg:col-span-6 flex flex-col justify-between gap-4 lg:grid grid-cols-1 lg:grid-cols-3 ">
                                
                                <div class="flex flex-col lg:grid grid-cols-2 col-span-2 gap-3">
                                    
                                    {{-- TITLE --}}
                                    <div class="">
                                        <a  href="{{route('product', $lineItem->product)}}" 
                                            class="text-xl font-bold hover:text-primary-500">
                                            {{ $lineItem->product->title }}
                                        </a>
                                        <p class="text-gray-400 line-clamp-3">{{ $lineItem->product->description }}</p>
                                    </div>

                                    {{-- PRICE --}}
                                    <div class="flex justify-start lg:justify-center items-center gap-2">
                                        <x-product.price :product="$lineItem->product" />
                                    </div>
                                </div>

                                <div class="flex justify-start lg:justify-end items-center gap-3">
                                    
                                    {{-- QUANTITY --}}
                                    <div class="quantity-container cursor-pointer relative border border-gray-300 rounded-md min-w-14 px-2 ">
                
                                        <form
                                            action="{{route('cart.quantity', $lineItem->product->id)}}"
                                            data-action="submit->cart#quantity"
                                            method="post"  
                                            class="quantity-menu hidden absolute bottom-8 left-0 w-full max-h-28 overflow-y-auto border 
                                            border-gray-200 bg-white rounded-md">
                                            @csrf
                                            @method('PATCH')
                                            @for ($i =0; $i <= $lineItem->product->stock; $i++)
                                                <button class="w-full text-lg {{ $lineItem->quantity == $i 
                                                    ? 'bg-primary-500 text-white' 
                                                    : 'hover:bg-gray-100 hover:text-black'}}"
                                                    type="submit"
                                                    name="quantity"
                                                    value="{{ $i }}">
                                                    {{ $i }}
                                                </button>
                                            @endfor
                                        </form>

                                        <div class="flex justify-end items-center gap-2" data-action="click->lineItemQuantity#openMenu">
                                            <div class="quantity text-xl">
                                                {{$lineItem->quantity}}
                                            </div>
                                            <div>
                                                <x-heroicon-o-chevron-down class="closed-chevron inline-block w-4 h-4"/>
                                                <x-heroicon-o-chevron-up class="open-chevron inline-block w-4 h-4 hidden"/>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- WISHLIST TOGGLE BUTTON --}}
                                    @auth   
                                        <x-form.wishlist-toggle 
                                            :product="$lineItem->product" 
                                            :isWishlisted="$wishlistedProductsIds->contains($lineItem->product->id)"/>
                                    @endauth
                                    
                                    {{-- REMOVE FROM CART BUTTON --}}
                                    <div>
                                        <form class="w-full" method='post' data-action="submit->cart#delete" action="{{ route('cart.delete', $lineItem->product->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">
                                                <x-heroicon-o-trash class="inline-block w-7 h-7 text-gray-500"/>
                                            </button>
                                        </form>
                                    </div>
                                </div>  
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="{{$cart->isNotEmpty() ? 'hidden' : 'flex'}} empty-cart-message bg-white h-96 px-5 py-5 flex justify-center items-center text-gray-500">
                    Your Cart is empty.
                </div>
            </div>
            <div id="order-summary-container" class="col-span-full lg:col-span-2 bg-white p-5">
                <div class="flex flex-col justify-center items-start gap-2 *:w-full">
                    <h2 class="text-xl font-semibold mb-5">Order Summary</h2>
                    
                    <div class="flex justify-between items-center">
                        <span>Subtotal (VAT included):</span>
                        <span class="cart-subtotal">{{number_format($cartSubtotal, 2)}}$</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Shipping Fee:</span>
                        <span id="shipping-fee">{{$shippingFee > 0 ? number_format($shippingFee , 2) . '$' : 'Free'}}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Payment Fee:</span>
                        <span id="payment-fee">{{number_format($paymentFee , 2) . '$'}}</span>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold">Total:</span>
                            <span class="cart-total font-bold">{{number_format($cartTotal, 2)}}$</span>
                        </div>  
                        
                        <div class="flex justify-between items-center *:text-xs *:text-gray-600">
                            <span>VAT included {{ config('app.vat_rate') * 100 }}%</span>
                            <span id="vat-price">{{number_format($cartSubtotal * config('app.vat_rate'), 2)}}$</span>
                        </div> 
                    </div>
                    <button id="checkout-button" @disabled($cart->count() <= 0) class="w-full mt-2 bg-black disabled:bg-black/40 border-2 border-black disabled:border-none disabled:hover:bg-black/40 hover:bg-white 
                        hover:text-black disabled:text-white text-white duration-300">
                        <a href="{{ route('checkout.login') }}" class="text-center block w-full h-full px-4 py-4">Proceed to Checkout</a> 
                    </button>                
                </div>
            </div>
        </div>

        <div class="hidden fixed bottom-10 w-full left-0 right-0 px-6">
            <div class="h-28 bg-gradient-to-br from-red-500 to-pink-500 rounded-md shadow-md ">

            </div>
        </div>
    </div>
</x-layout>