<header class="shadow-lg w-full" data-controller="responsive-nav-menu">
    
    <div class="relative p-5 max-w-screen-xl m-auto flex justify-between items-center gap-5">

        @if(session('success'))    
            <div id='flash-message' class="rounded-lg bg-red-500 text-white p-5  absolute -bottom-20 right-5 ">
                {{ session('success') }}
            </div>
        @endif
        
        <script>
            const flashMessage = document.getElementById('flash-message');

            if(flashMessage) {
                setTimeout( () => {
                    flashMessage.style.display = 'none';
                }, 3000);
            }
        </script>
        
        <div class="min-w-[40px]" >
            <img src="{{ asset('svg/logo.svg') }}" alt="Logo" class="w-10 h-10">
        </div>

        <div class="w-full hidden md:flex justify-between items-center gap-20">
            <nav class="flex justify-center items-center gap-5">
                    <x-nav.link href="/" :active="request()->is('/')" >Home</x-nav.link>
                    <x-nav.link href="/products" :active="request()->is('products')" >Products</x-nav.link>
                    <x-nav.link href="/contact" :active="request()->is('contact')" >Contact</x-nav.link>
            </nav>
            <div class="flex justify-center items-center gap-5">
                <div>
                    <x-heroicon-c-magnifying-glass class="w-7 h-7 text-gray-500 hover:text-primary-500"/>
                </div>
                <div class="relative">
                    <div class="absolute -top-4 -right-2 bg-gray-500 text-white rounded-full w-5 h-5 text-xs flex justify-center items-center" >
                        2
                    </div>
                    <x-heroicon-o-heart class="w-7 h-7 text-gray-500 hover:text-primary-500"/>
                </div>
                <div class="relative">
                    <div class="absolute -top-4 -right-2 bg-gray-500 text-white rounded-full w-5 h-5 text-xs flex justify-center items-center" >
                            22
                    </div>
                    <x-heroicon-c-shopping-bag class="w-7 h-7 text-gray-500 hover:text-primary-500"/>
                </div>
                <div class="relative group">
                    <div class="flex justify-center items-center">
                        <x-heroicon-c-user class="w-7 h-7 text-gray-500 group-hover:text-primary-500"/>
                        <x-heroicon-c-chevron-down class="-translate-x-1 w-4 h-4 text-gray-500 group-hover:text-primary-500"/>

                        @if(Auth::check())
                            <h2 class="text-xs">{{auth()->user()->name }}</h2>
                        @else
                            <h2 class="text-xs">Guest</h2>
                        @endif

                    </div>
                    <div class="min-w-32 absolute top-7 -right-3 bg-white border border-gray-200 p-4 group-hover:block hidden">
                        <ul class="space-y-2">
                            @guest
                                <li>
                                    <a href="{{route('login')}}" class="inline-block w-full hover:text-primary-500">Login</a>
                                </li> 
                                <li>
                                    <a href="{{route('signup')}}" class="inline-block w-full hover:text-primary-500">Sign up</a>
                                </li> 
                            @endguest
                            @auth 
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="hover:text-primary-500">Log out</button>
                                    </form>
                                </li>
                            @endauth
                           
                        </ul>
                    </div>
                </div>
            </div> 
        </div>

        {{-- RESPONSIVE --}}  
        <x-nav.mobile-header />
            
    </div>
</header>
