<x-layout :hideHeader="true">
    <div class="mt-16 max-w-screen-xl m-auto p-6 bg-white relative ">
        <a href="/" class="block w-fit m-auto" >
            <img src="{{ asset('svg/logo.svg') }}" alt="Logo" class="w-20 h-20">
        </a>

        <h1 class="text-center text-5xl font-semibold mt-16">Checkout</h1>
        
        <x-checkout.steps :$steps :$currentStep />      

        <div class="w-full grid grid-cols-2 mt-10 py-14 gap-20 md:gap-0">
            <div class="col-span-full order-2 md:order-1 md:col-span-1 px-10 border-r border-gray-100">
                <h1 class="text-center text-3xl font-semibold">Login</h1>
                <form action="/login" method="POST" class="mt-10">
                    @csrf

                    <x-form.input label="E-mail" name="email" placeholder="E-mail"/>

                    <x-form.input label="Password" name="password" placeholder="Password" />
                                    
                    @if ($errors->any())
                        <div class="text-red-500">
                            {{ $errors->first('login') }}
                        </div>
                    @endif
                    <button type="submit" class="font-semibold mt-5 rounded-full px-5 py-4 w-full text-white text-base bg-gradient-to-br from-red-500 to-pink-500">
                        Login
                    </button>
                    <div class="mt-5 flex justify-between items-center">
                        <div class="text-sm has-[:checked]:text-red-600">
                            <input id="remember-me" type="checkbox" class="accent-red-500 checked:border-red-500">
                            <label for="remember-me" class="font-normal text-sm">Remember Me</label>
                        </div>
                        <a class="text-sm hover:text-primary-500">Forgot Password?</a>
                    </div>
                </form>
            </div>

            <div class="col-span-full order-2 md:order-1 md:col-span-1  px-10 bg-white flex flex-col">
                <h1 class="text-center text-3xl font-semibold">Guest</h1>
                <div class="mt-10 flex-1 flex flex-col pb-10">
                    @csrf   

                    <div class="flex-1 flex justify-center items-center bg-gray-50 min-h-[180px]">Proceed to checkout as guest.</div>

                    <a href="{{route('checkout.customer')}}" class="block font-semibold mt-5 rounded-full px-5 py-4 w-full text-white text-center text-base bg-gradient-to-br from-red-500 to-pink-500">
                        Continue as Guest
                    </a>
                    
                </div>
            </div>
        </div>
    </div>
</x-layout>