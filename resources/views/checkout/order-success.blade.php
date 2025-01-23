<x-layout :hideHeader="true" :hideFooter="true">
    <div class="fixed top-0 bottom-0 right-0 left-0 flex justify-center items-center">
        <div class="max-w-[600px] border-2 border-gray-400 rounded-lg w-3/4">
            <div class="m-auto px-5 py-20 relative">
                <a href="/" class="block w-fit m-auto" >
                    <img src="{{ asset('svg/logo7.png') }}" alt="Logo" class="w-52">
                </a>

                <h1 class="text-center text-3xl sm:text-4xl font-semibold mt-12">Thank You for Your Order!</h1>
                <p class="mt-5 text-center text-gray-600 text-xl">Your order has been successfully completed.</p>
                <a href="{{route('home')}}" class="block text-center hover:underline text-gray-600 font-semibold hover:text-primary-500 mt-10">Return to Homepage</a>
            </div>
        </div>
    </div>
</x-layout>