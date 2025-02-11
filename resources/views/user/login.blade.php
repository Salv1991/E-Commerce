<x-layout>
    <div class="bg-gray-50 p-5 fixed top-[89px] right-0 bottom-0 left-0 flex justify-center items-center overflow-y-auto">

        <div class="w-full max-w-screen-xl m-auto grid grid-cols-2 shadow-lg">
            {{-- LEFT --}}
            <div class="col-span-full order-2 md:order-1 md:col-span-1 py-14 px-6 md:px-10 bg-white" data-controller="disableButton">
                <h1 class="text-3xl font-semibold">Login </h1>
                <form action="/login" method="POST" class="mt-10" data-action="submit->disableButton#disable">
                    @csrf

                    <x-form.input required label="E-mail" name="email" value="{{old('email')}}" placeholder="E-mail"/>
                    <span class="text-gray-500 text-xs pl-2">
                        Default admin e-mail: <strong>'admin@admin.com'</strong>
                    </span>

                    <x-form.input required type="password" label="Password" name="password" value="{{old('password')}}" placeholder="Password" />
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

            {{-- RIGHT --}}
            <div class="col-span-full order-1 md:order-2 md:col-span-1 py-14 px-6 md:px-10 bg-gradient-to-br from-red-500 to-pink-500 flex justify-center items-center">
                <div class="text-white flex flex-col items-center gap-5">
                    <h2 class="text-4xl font-bold text-center">Welcome back!</h2>
                    <h3 class="text-lg text-center">Don't have an account?</h3>
                    <a href="{{route('signup')}}" class="font-semibold border border-white rounded-full px-4 py-2 inline-block hover:bg-white hover:text-primary-500 duration-150">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</x-layout>