<x-layout>
    <div class="mt-16 max-w-screen-xl m-auto p-6">

        <h1 class="text-center text-4xl font-semibold">My Cart</h1>

        <div id="cart-container" class="mt-14 grid grid-cols-6 items-start gap-8 bg-gray-100">
            <div 
                data-controller="wishlist lineItemQuantity" 
                data-filter-target="productsContainer" 
                class="col-span-full lg:col-span-4  divide-y-2 bg-white px-5">

                @if ($cart->isNotEmpty())
                    @foreach ($cart as $lineItem )
                        <div class="bg-white grid grid-cols-7 py-5 gap-4">
                          
                            <a href="{{route('product', $lineItem['product'])}}" 
                                class="w-full h-full overflow-hidden aspect-[.75] col-span-2 lg:col-span-1">
                                <img 
                                    class="h-full w-full object-cover object-center" 
                                    src="{{ asset('storage/' . ($lineItem['product']->images->isNotEmpty() 
                                        ? $lineItem['product']->images->first()->image_path 
                                        : 'products/placeholder.jpg') )}}" 
                                    alt="Product Image">  
                            </a>

                            <div class="col-span-5 lg:col-span-6 flex flex-col justify-between gap-4 lg:grid grid-cols-1 lg:grid-cols-3 ">
                                
                                <div class="flex flex-col lg:grid grid-cols-2 col-span-2 gap-3">
                                    
                                    {{-- TITLE --}}
                                    <div class="">
                                        <a  href="{{route('product', $lineItem['product'])}}" 
                                            class="text-xl font-bold hover:text-primary-500">
                                            {{ $lineItem['product']->title }}
                                        </a>
                                        <p class="text-gray-400 line-clamp-3">{{ $lineItem['product']->description }}</p>
                                    </div>

                                    {{-- PRICE --}}
                                    <div class="flex justify-start lg:justify-center items-center gap-2">
                                        <x-product.price :product="$lineItem['product']" />
                                    </div>
                                </div>

                                <div class="flex justify-start lg:justify-end items-center gap-3">
                                    
                                    {{-- QUANTITY --}}
                                    <div data-action="" class="quantity-container cursor-pointer relative border border-gray-300 rounded-md min-w-14 px-2 ">
                                        
                                        <form
                                            action="{{route('cart.quantity', $lineItem['id'])}}"
                                            method="post" 
                                            data-lineItemQuantity-target="quantityMenu" 
                                            class="quantity-menu hidden absolute bottom-8 left-0 w-full max-h-28 overflow-y-auto border 
                                            border-gray-200 bg-white rounded-md">
                                            @csrf
                                            @method('PATCH')
                                            @for ($i =0; $i <= $lineItem['product']->stock; $i++)
                                                <button class="w-full text-lg {{ $lineItem['quantity'] == $i 
                                                    ? 'bg-primary-500 text-white' 
                                                    : 'hover:bg-gray-100 hover:text-black'}}"
                                                    name="quantity"
                                                    value="{{ $i }}">
                                                    {{ $i }}
                                                </button>
                                            @endfor
                                        </form>

                                        <div class="flex justify-end items-center gap-2" data-action="click->lineItemQuantity#openMenu">
                                            <div class="text-xl">
                                                {{$lineItem['quantity']}}
                                            </div>
                                            <div>
                                                <x-heroicon-o-chevron-down class="closed-chevron inline-block w-4 h-4"/>
                                                <x-heroicon-o-chevron-up class="open-chevron inline-block w-4 h-4 hidden"/>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- WISHLIST TOGGLE BUTTON --}}
                                    <x-form.wishlist-toggle 
                                        :product='$lineItem['product']' 
                                        :isWishlisted="$wishlistedProductsIds->contains($lineItem['product']->id)"/>
                                    
                                    {{-- REMOVE FROM CART BUTTON --}}
                                    <div>
                                        <form class="w-full" method='post' action="{{ route('cart.delete', $lineItem['product']->id) }}">
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
                @else
                    <div class="bg-white h-96 px-5 py-5 flex justify-center items-center">
                        <span class="text-gray-500"> Your Cart is empty. </span>
                    </div>
                @endif
            </div>
            <div class="col-span-full lg:col-span-2 bg-white p-5">
                <div class="flex flex-col justify-center items-start gap-2 *:w-full">
                    <h2 class="text-lg font-bold">Order Summary</h2>
                    
                    <div class="flex justify-between items-center">
                        <span>Subtotal (VAT included):</span>
                        <span>{{$cartTotal}}$</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Shipping Fee:</span>
                        <span>3,40$</span>
                    </div>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-lg font-bold">Total:</span>
                        <span>{{$cartTotal}}$</span>
                    </div>
                    <button class="w-full mt-2 px-4 py-4 bg-black border-2 border-black hover:bg-white 
                        hover:text-black  text-white duration-300">
                        <span class="text-center">Proceed to Checkout</span> 
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