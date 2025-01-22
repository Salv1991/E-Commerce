<x-layout :hideHeader="true" :hideFooter="true">
    <x-checkout.layout :steps="$steps" :currentStep="$currentStep" title="Customer Information">
        <div class="w-full grid grid-cols-2 py-14 gap-10">
            {{-- CUSTOMER INFORMATION --}}
            <form id="customer-form" action="{{route('checkout.customer.submit') }}" method="post" class="col-span-full md:col-span-1">
                @csrf
                <x-checkout.section title="Contact Information" >
                    @auth         
                        <x-form.input 
                            required
                            readonly
                            label="E-mail" 
                            name="email" 
                            value="{{ $customerData['email'] ?? old('email') }}" 
                            placeholder="E-mail"/> 
                    @endauth

                    @guest
                        <x-form.input 
                            required 
                            label="E-mail" 
                            name="email" 
                            value="{{ $customerData['email'] ?? old('email') }}" 
                            placeholder="E-mail"/>
                    @endguest
                </x-checkout.section>

                <x-checkout.section class="mt-10" title="Billing address" >                    
                    <x-form.input required label="Name" name="name" value="{{ $customerData['name'] ?? old('name') }}" placeholder="Name"/>
                    <div class="grid grid-cols-3 gap-4">
                        <x-form.input required class="col-span-full sm:col-span-2" label="Address" name="address" value="{{ $customerData['address'] ?? old('address') }}" placeholder="Address"/>
                        <x-form.input required class="col-span-full sm:col-span-1" label="Postal code" name="postal_code" value="{{ $customerData['postal_code'] ?? old('postal_code') }}" placeholder="Postal code"/>
                    </div>
                    <x-form.input label="Floor" name="floor" value="{{ $customerData['floor'] ?? old('floor') }}" placeholder="Optional"/>
                    <div class="grid grid-cols-2 gap-4">                             
                        <x-form.input required class="col-span-full sm:col-span-1" label="Country" name="country" value="{{ $customerData['country'] ?? old('country') }}" placeholder="Country"/>
                        <x-form.input required class="col-span-full sm:col-span-1" label="City" name="city" value="{{ $customerData['city'] ?? old('city') }}" placeholder="City"/>
                        <x-form.input required class="col-span-full sm:col-span-1" label="Mobile phone" name="mobile" value="{{ $customerData['mobile'] ?? old('mobile') }}" placeholder="Mobile phone"/>
                        <x-form.input class="col-span-full sm:col-span-1" label="Alternative phone" name="alternative_phone" value="{{ $customerData['alternative_phone'] ?? old('alternative_phone') }}" placeholder="Alternative phone"/>
                    </div>
                </x-checkout.section>
            </form>

            {{-- ORDER --}}
            <section class="col-span-full md:col-span-1"> 

                <x-checkout.order-items :cart="$cartData['cart']" :cartCount="$cartData['cartCount']" />

                <div class="text-right border-t border-t-black pt-10">
                    <span class="text-base font-semibold">Subtotal: {{ number_format($cartData['cartSubtotal'], 2) }}$</span>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <a href="{{ route('cart') }}" class="col-span-full sm:col-span-1 block bg-transparent border hover:underline underline-offset-4 border-black text-center text-sm py-3 px-5">BACK</a>
                    <button
                        onclick="document.getElementById('customer-form').submit();" 
                        type="submit" 
                        class="col-span-full sm:col-span-1 bg-black border border-black text-white text-center text-sm py-3 px-5 hover:underline underline-offset-4">
                        NEXT
                    </button>
                </div>
            </section>   
        </div>
    </x-checkout.layout>
</x-layout>

