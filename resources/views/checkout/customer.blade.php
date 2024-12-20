<x-layout :hideHeader="true">
    <div class="mt-16 max-w-screen-xl m-auto p-6 bg-white relative ">
        <a href="/" class="block w-fit m-auto" >
            <img src="{{ asset('svg/logo.svg') }}" alt="Logo" class="w-20 h-20">
        </a>

        <h1 class="text-center text-3xl sm:text-5xl font-semibold mt-16">Customer Information</h1>
        
        <x-checkout.steps :$steps :$currentStep />      

        <div class="w-full grid grid-cols-2 mt-10 py-14 gap-20 md:gap-0">
            
        </div>
    </div>
</x-layout>