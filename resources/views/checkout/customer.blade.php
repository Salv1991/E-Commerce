<x-layout :hideHeader="true">
    <div class="bg-white">
        <div class="mb-10 max-w-screen-xl m-auto px-5 pt-20 relative">
            <a href="/" class="block w-fit m-auto" >
                <img src="{{ asset('svg/logo.svg') }}" alt="Logo" class="w-20 h-20">
            </a>

            <h1 class="text-center text-3xl sm:text-5xl font-semibold mt-16">Customer Information</h1>
            
            <x-checkout.steps :$steps :$currentStep />      

            <div class="w-full grid grid-cols-2 mt-10 py-14 gap-10">
                {{-- CUSTOMER INFORMATION --}}
                <form id="customer-form" action="{{route('checkout.customer.submit')}}" method="post">
                    @csrf
                    <div>
                        <h2 class="text-xl font-semibold border-b border-black pb-2">Contact Information</h2>  
                        <x-form.input required label="E-mail" name="email" placeholder="E-mail"/>
                    </div>
                    <div class="mt-10">
                        <h2 class="text-xl font-semibold border-b border-black pb-2">Billing address</h2>  
                        <x-form.input required label="Name" name="name" placeholder="Name"/>
                        <div class="grid grid-cols-3 gap-4">
                            <x-form.input required class="col-span-2" label="Address" name="address" placeholder="Address"/>
                            <x-form.input required label="Postal code" name="postal_code" placeholder="Postal code"/>
                        </div>
                        <x-form.input label="Floor" name="floor" placeholder="Optional"/>
                        <div class="grid grid-cols-2 gap-4">                             
                            <x-form.input required label="Country" name="country" placeholder="Country"/>
                            <x-form.input required label="City" name="city" placeholder="City"/>
                            <x-form.input required label="Mobile phone" name="mobile" placeholder="Mobile phone"/>
                            <x-form.input label="Alternative phone" name="alternative_phone" placeholder="Alternative phone"/>
                        </div>
                    </div>
                </form>

                {{-- ORDER --}}
                <div class=""> 
                    <h2 class="text-xl font-semibold border-b border-black pb-2">Your order <span class="text-sm text-gray-500">( {{$cartCount}} )</span></h2> 
                    <div class="h-full max-h-[860px] overflow-y-auto mt-2 divide-y-2">
                        @if ($cart->isNotEmpty())
                            @foreach ($cart as $lineItem )
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
                    <div class="{{$cart->isNotEmpty() ? 'hidden' : 'flex'}} empty-cart-message bg-white h-96 px-5 py-5 flex justify-center items-center text-gray-500">
                        Your Cart is empty.
                    </div>
                </div>

                <div class="col-span-1 col-start-2 grid grid-cols-2 gap-4 mt-6">
                    <a href="{{route('cart')" class="block bg-transparent border border-black text-center py-3 px-5">BACK</a>
                    <button
                        onclick="document.getElementById('customer-form').submit();" 
                        type="submit" 
                        class="bg-black border border-black text-white text-center py-3 px-5">
                        NEXT
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-layout>