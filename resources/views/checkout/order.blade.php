<x-layout :hideHeader="true">
{{-- MESSAGES --}}
        
    <div class="bg-white min-h-[100vh] relative">

        {{-- ERROR MESSAGE --}}
        <div class="absolute top-5 right-5">
            <x-error-message />
        </div>

        <div class="mb-10 max-w-screen-xl m-auto px-5 pt-20 relative" data-controller="order">
            <a href="/" class="block w-fit m-auto" >
                <img src="{{ asset('svg/logo.svg') }}" alt="Logo" class="w-20 h-20">
            </a>

            <h1 class="text-center text-3xl sm:text-5xl font-semibold mt-16">Order Information</h1>
            
            <x-checkout.steps :$steps :$currentStep />  

            <div class="w-full grid grid-cols-2 mt-10 py-14 gap-10">
                {{-- Order INFORMATION --}}
                <div class="col-span-full md:col-span-1">
                    <x-checkout.section title="Billing address" >
                        <div>{{ $customerData['name'] }}</div>
                        <div>{{ $customerData['address'] }} {{ $customerData['postal_code'] }}</div>
                        <div>{{ $customerData['city'] }}, {{ $customerData['country'] }}</div>
                        <div>Phone: {{ $customerData['mobile'] }}</div>
                        <a href="{{ route('checkout.customer') }}" class="border border-black px-16 py-3 tex-lg mt-2 inline-block hover:underline">Edit</a>
                    </x-checkout.section>
                    
                <form action="{{ route('checkout.order.complete') }}" method="post" id="shipping-payment-form">
                    @csrf                        
                    <x-checkout.section data-checkout-shipping-method class="mt-10" title="Shipping method" > 
                            <div class="flex flex-col gap-2">              
                                @foreach (config('app.shipping_methods') as $key => $shippingMethod)
                                    <label>
                                        <div class="flex items-center justify-start gap-2">
                                            <input 
                                                type="radio" 
                                                name="shipping_method" 
                                                value="{{ $key }}"
                                                data-action="change->order#updateShippingMethod"
                                                @checked( $cartData['shipping_method'] == $key)/>
                                            <span>{{ $shippingMethod['title'] }}</span>
                                        </div>                                      
                                    </label>
                                @endforeach
                                
                                @if ($errors->has('shipping_method'))
                                    <span class="pl-2 text-sm text-red-500">
                                        {{ $errors->first('shipping_method') }}
                                    </span>
                                @endif
                            </div>  
                        </x-checkout.section>

                        <x-checkout.section data-checkout-payment-method class="mt-10" title="Payment method" >                    
                            <div class="flex flex-col gap-2">              
                                @foreach (config('app.payment_methods') as $key => $paymentMethod)
                                    <label>
                                        <div class="flex items-center justify-start gap-2">
                                            <input 
                                                type="radio" 
                                                name="payment_method" 
                                                value="{{ $key }}"
                                                data-action="change->order#updatePaymentMethod"
                                                @if ( $cartData['payment_method'] == $key)
                                                    checked
                                                @endif>
                                            <span>{{ $paymentMethod['title'] }}</span>
                                        </div>
                                    </label>
                                @endforeach

                                @if ($errors->has('payment_method'))
                                    <span class="pl-2 text-sm text-red-500">
                                        {{ $errors->first('payment_method') }}
                                    </span>
                                @endif
                            </div>  
                    </x-checkout.section>
                    </form>
                </div>

                {{-- ORDER --}}
                <section class="col-span-full md:col-span-1"> 
                    <h2 class="text-xl font-semibold border-b border-black pb-2">
                        Your order 
                        <span class="text-sm text-gray-500">( {{ $cartData['cartCount'] }} )</span>
                    </h2> 
                    <div class="max-h-[680px] overflow-y-auto my-2 divide-y-2">
                        @if ( $cartData['cart']->isNotEmpty())
                            @foreach ( $cartData['cart'] as $lineItem )
                                <div id="product-{{$lineItem->product->id}}" 
                                    data-teaser-{{$lineItem->product->id}} 
                                    class="bg-white grid grid-cols-7 py-5 gap-2">
                                    
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
                                        <div class="flex flex-col lg:grid grid-cols-2 col-span-3 gap-3"> 
                                            
                                            {{-- TITLE --}}
                                            <div class="">
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
                                            <div class="flex justify-start lg:justify-end items-center gap-2 pr-4">
                                                <x-product.price :product="$lineItem->product" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div> 
                    <div class="{{ $cartData['cart']->isNotEmpty() ? 'hidden' : 'flex'}} empty-cart-message bg-white h-96 px-5 py-5 flex justify-center items-center text-gray-500">
                        Your Cart is empty.
                    </div>
                    <div class="text-right border-t border-t-black pt-10 flex flex-col">
                        <span class="text-base">Subtotal:  
                            <span id="subtotal">{{ number_format( $cartData['cartSubtotal'], 2) }}</span>
                            $
                        </span>

                        <span class="text-base">
                            Shipping Fee: 
                            <span id="shipping-fee">{{  $cartData['shipping_fee'] == 0 ? 'Free' : number_format( $cartData['shipping_fee'], 2) . ' $' }}</span>
                        </span>

                        <span class="text-base">
                            Payment Fee: 
                            <span id="payment-fee">{{ $cartData['payment_fee'] == 0 ? 'Free' : number_format( $cartData['payment_fee'], 2). ' $' }}</span>
                        </span>

                        <span class="text-lg font-semibold mt-2">
                            Total: 
                            <span id="total">{{ number_format( $cartData['cartTotal'], 2) }}</span>
                            $
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-6 ">
                        <a href="{{ route('checkout.customer') }}" class="block bg-transparent border hover:underline border-black text-center text-sm py-3 px-5">BACK</a>         
                        <button
                            onclick="document.getElementById('shipping-payment-form').submit();" 
                            type="submit" 
                            class="bg-black border border-black hover:underline text-white text-center text-sm py-3 px-5">
                            COMPLETE ORDER
                        </button>
                    </div>
                </section>   
            </div>
        </div>
    </div>
</x-layout>