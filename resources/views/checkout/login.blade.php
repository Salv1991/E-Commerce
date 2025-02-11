<x-layout :hideHeader="true">
    <x-checkout.layout :steps="$steps" :currentStep="$currentStep" title="Checkout">
    
        <div class="w-full grid grid-cols-2 mt-10 py-14 gap-20 md:gap-0" data-controller="disableButton">
            <div class="col-span-full order-2 md:order-1 md:col-span-1 md:px-8">
                <h1 class="text-center text-3xl font-semibold">Login</h1>
                <form action="/login" method="POST" class="mt-10" data-action="submit->disableButton#disable">
                    @csrf

                    <x-form.input required label="E-mail" name="email" value="{{old('email')}}" placeholder="E-mail"/>
                    <span class="text-gray-500 text-xs pl-2">
                        Default admin e-mail: <strong>'admin@admin.com'</strong>
                    </span>

                    <x-form.input type="password" required label="Password" name="password" value="{{old('password')}}" placeholder="Password" />
                    <span class="text-gray-500 text-xs pl-2">
                        Default admin password: <strong>'admin'</strong>
                    </span>

                    @if ($errors->any())
                        <div class="text-red-500">
                            {{ $errors->first('login') }}
                        </div>
                    @endif
                    <button type="submit" class="font-semibold cursor-pointer mt-5 rounded-full px-5 py-4 w-full text-white text-base bg-gradient-to-br from-red-500 to-pink-500">
                        Login
                    </button>
                </form>
            </div>

            <div class="col-span-full order-2 md:order-1 md:col-span-1 md:px-8 bg-white flex flex-col">
                <h1 class="text-center text-3xl font-semibold">Guest</h1>
                <div class="mt-10 flex-1 flex flex-col">
                    @csrf   

                    <div class="flex-1 flex justify-center items-center bg-gray-50 min-h-[180px]">Proceed to checkout as guest.</div>

                    <a href="{{route('checkout.customer')}}" class="block font-semibold mt-5 rounded-full px-5 py-4 w-full text-white text-center text-base bg-gradient-to-br from-red-500 to-pink-500">
                        Continue as Guest
                    </a>
                    
                </div>
            </div>
    </x-checkout.layout>
</x-layout>