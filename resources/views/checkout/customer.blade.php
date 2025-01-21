<x-layout :hideHeader="true" :hideFooter="true">
    <div class="bg-white relative">

        {{-- ERROR MESSAGE --}}
        <div class="absolute top-5 right-5">
            <x-error-message />
        </div>

        <div class="max-w-screen-xl m-auto px-5 pt-10 relative">
            <a href="/" class="block w-fit m-auto" >
                <img src="{{ asset('svg/logo7.png') }}" alt="Logo" class="h-36">
            </a>

            <h1 class="text-center text-3xl sm:text-5xl font-semibold mt-10">Customer Information</h1>
            
            <x-checkout.steps :$steps :$currentStep />      

            <div class="w-full grid grid-cols-2 mt-10 py-14 gap-10">
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
                            <x-form.input required class="col-span-2" label="Address" name="address" value="{{ $customerData['address'] ?? old('address') }}" placeholder="Address"/>
                            <x-form.input required label="Postal code" name="postal_code" value="{{ $customerData['postal_code'] ?? old('postal_code') }}" placeholder="Postal code"/>
                        </div>
                        <x-form.input label="Floor" name="floor" value="{{ $customerData['floor'] ?? old('floor') }}" placeholder="Optional"/>
                        <div class="grid grid-cols-2 gap-4">                             
                            <x-form.input required label="Country" name="country" value="{{ $customerData['country'] ?? old('country') }}" placeholder="Country"/>
                            <x-form.input required label="City" name="city" value="{{ $customerData['city'] ?? old('city') }}" placeholder="City"/>
                            <x-form.input required label="Mobile phone" name="mobile" value="{{ $customerData['mobile'] ?? old('mobile') }}" placeholder="Mobile phone"/>
                            <x-form.input label="Alternative phone" name="alternative_phone" value="{{ $customerData['alternative_phone'] ?? old('alternative_phone') }}" placeholder="Alternative phone"/>
                        </div>
                    </x-checkout.section>
                </form>

                {{-- ORDER --}}
                <section class="col-span-full md:col-span-1"> 
                    <h2 class="text-xl font-semibold border-b border-black pb-2">
                        Your order 
                        <span class="text-sm text-gray-500">( {{ $cartData['cartCount'] }} )</span>
                    </h2> 
                    <div class="max-h-[680px] overflow-y-auto my-2 divide-y-2">
                        @if ($cartData['cart']->isNotEmpty())
                            @foreach ($cartData['cart'] as $lineItem )
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
                    <div class="{{$cartData['cart']->isNotEmpty() ? 'hidden' : 'flex'}} empty-cart-message bg-white h-96 px-5 py-5 flex justify-center items-center text-gray-500">
                        Your Cart is empty.
                    </div>
                    <div class="text-right border-t border-t-black pt-10">
                        <span class="text-base font-semibold">Subtotal: {{ number_format($cartData['cartSubtotal'], 2) }}$</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-6">
                        <a href="{{ route('cart') }}" class="block bg-transparent border hover:underline border-black text-center text-sm py-3 px-5">BACK</a>
                        <button
                            onclick="document.getElementById('customer-form').submit();" 
                            type="submit" 
                            class="bg-black border border-black text-white text-center text-sm py-3 px-5 hover:underline">
                            NEXT
                        </button>
                    </div>
                </section>   
            </div>
        </div>
    </div>
</x-layout>