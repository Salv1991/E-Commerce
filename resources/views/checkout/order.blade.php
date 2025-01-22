<x-layout :hideHeader="true" :hideFooter="true">
    <x-checkout.layout :steps="$steps" :currentStep="$currentStep" title="Order Information">
        <div class="w-full grid grid-cols-2 py-14 gap-10">
        {{-- Order INFORMATION --}}
        <div class="col-span-full md:col-span-1">
        <x-checkout.section title="Billing address" >
            <div>{{ $customerData['name'] }}</div>
            <div>{{ $customerData['address'] }} {{ $customerData['postal_code'] }}</div>
            <div>{{ $customerData['city'] }}, {{ $customerData['country'] }}</div>
            <div>Phone: {{ $customerData['mobile'] }}</div>
            <a href="{{ route('checkout.customer') }}" class="border border-black text-center px-16 py-3 text-base mt-2 inline-block hover:underline w-full xs:w-fit underline-offset-4">Edit</a>
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

        <x-checkout.order-items :cart="$cartData['cart']" :cartCount="$cartData['cartCount']" />

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
            <a href="{{ route('checkout.customer') }}" class="col-span-full xs:col-span-1 block bg-transparent border hover:underline underline-offset-4 border-black text-center text-sm py-3 px-5">BACK</a>         
            <button
                onclick="document.getElementById('shipping-payment-form').submit();" 
                type="submit" 
                class="col-span-full xs:col-span-1 bg-black border border-black hover:underline underline-offset-4 text-white text-center text-sm py-3 px-5">
                COMPLETE ORDER
            </button>
        </div>
        </section>   
        </div>
    </x-checkout.layout>
</x-layout>