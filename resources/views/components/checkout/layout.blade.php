<div class="bg-white relative">

    {{-- ERROR MESSAGE --}}
    <div class="absolute top-5 right-5">
        <x-error-message />
    </div>

    <div class="max-w-screen-xl m-auto px-5 pt-10 relative">
        <a href="/" class="block w-fit m-auto" >
            <img src="{{ asset('svg/logo7.png') }}" alt="Logo" class="h-16 xs:h-20 md:h-36">
        </a>

        <h1 class="text-center text-2xl xs:text-3xl md:text-5xl font-semibold mt-10">{{ $title }}</h1>
        
        <x-checkout.steps :$steps :$currentStep />      

        {{ $slot }}

    </div>
</div>
