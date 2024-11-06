<x-layout>
    <div class="bg-gray-50 p-5 fixed top-0 right-0 bottom-0 left-0 flex justify-center items-center overflow-y-auto">

        <div class="w-full max-w-screen-xl m-auto grid grid-cols-2 shadow-lg">
            {{-- LEFT --}}
            <div class="col-span-full order-2 md:order-1 md:col-span-1 py-14 px-10 bg-white">
                <h1 class="text-3xl font-semibold">Sign Up</h1>
                <form action="{{route('signup.create')}}" method="POST" class="mt-10">
                    @csrf
                    
                    <x-form.input label="Name" name="name" placeholder="Name" />
                    
                    <x-form.input label="E-mail" name="email" placeholder="E-mail" />
                       
                    <x-form.input label="Password" name="password" placeholder="Password" />
                    <x-form.input label="Confirm Password" name="password_confirmation" placeholder="Confirm Password" />

                    <!-- @if ($errors->any())
                        <div class="text-red-500">
                            {{ $errors->first('login') }}
                        </div>
                    @endif -->
                    <button type="submit" class="font-semibold mt-5 rounded-full px-5 py-4 w-full text-white text-base bg-gradient-to-br from-red-500 to-pink-500">
                        Sign Up
                    </button>
                </form>
            </div>

            {{-- RIGHT --}}
            <div class="col-span-full order-1 md:order-2 md:col-span-1 py-14 px-10 bg-gradient-to-br from-red-500 to-pink-500 flex justify-center items-center">
                <div class="text-white flex flex-col items-center gap-5">
                    <h2 class="text-4xl font-bold">Welcome!</h2>
                    <h3 class="text-lg">You already have an account?</h3>
                    <a href="{{route('login')}}" class="font-semibold border border-white rounded-full px-4 py-2 inline-block hover:bg-white hover:text-primary-500 duration-150">
                        Log In
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout>