<header class="shadow-lg w-full z-40" data-controller="responsive-nav-menu">
    
    <div class="relative p-5 max-w-screen-xl m-auto flex justify-between items-center gap-5">
        @if(session('success'))    
            <div id='flash-message' class="z-30 rounded-lg bg-red-500 text-white p-5  absolute -bottom-20 right-5 ">
                {{ session('success') }}
            </div>

            <script>
                const flashMessage = document.getElementById('flash-message');

                if (flashMessage) {
                    setTimeout( () => {
                        flashMessage.style.display = 'none';
                    }, 3000);
                }
            </script>
        @endif
        
        <div class="min-w-[40px]" >
            <img src="{{ asset('svg/logo.svg') }}" alt="Logo" class="w-10 h-10">
        </div>

        <div class="w-full flex justify-end md:justify-between items-center gap-20">
            <nav class="hidden md:flex justify-center items-center gap-5">
                {{-- HOME --}}
                <x-nav.link href="/" :active="request()->is('/')" >Home</x-nav.link>

                {{-- CATEGORIES --}}
                <li class="relative group/zeroDepth p-3">
                    <div class="flex justify-center items-center group-hover/zeroDepth:text-primary-500 cursor-pointer">
                        <h2 class="mr-1">Categories</h2>
                        <x-heroicon-c-chevron-down class="-translate-x-1 w-4 h-4 text-black group-hover/zeroDepth:text-primary-500"/>
                    </div>

                    <div class="z-10 min-w-48 absolute top-12 -right-3 bg-white border border-gray-200 p-3 group-hover/zeroDepth:block hidden">
                        <ul class="space-y-2">
                            {{-- ALL DEPTH CATEGORIES --}}
                            @foreach ($categories as $category)
                                <x-nav.category :$category />
                            @endforeach 
                        </ul>
                    </div>
                </li>

                <x-nav.link href="/contact" :active="request()->is('contact')" >Contact</x-nav.link>
            </nav>

            <div class="flex justify-center items-center gap-2">
                {{-- SEARCH --}}
                <div>
                    <x-heroicon-c-magnifying-glass class="w-7 h-7 text-gray-500 hover:text-primary-500"/>
                </div>

                {{-- WISHLIST --}}
                <a href="{{ route('wishlist') }}" class="relative group p-2">
                    <div id="wishlist-count" class="absolute -top-1 -right-1 bg-gray-500 group-hover:bg-primary-500 text-white 
                    rounded-full w-5 h-5 text-xs flex justify-center items-center">
                        {{auth()->user()?->wishlistedProducts()->count() ?? 0}}
                    </div>
                    <x-heroicon-o-heart class="w-7 h-7 text-gray-500 group-hover:text-primary-500"/>
                </a>

                {{-- CART --}}
                <div class="relative group p-2">
                    <div class="absolute -top-1 -right-1 bg-gray-500 group-hover:bg-primary-500 text-white rounded-full 
                    w-5 h-5 text-xs flex justify-center items-center" >
                        22
                    </div>
                    <x-heroicon-c-shopping-bag class="w-7 h-7 text-gray-500 group-hover:text-primary-500"/>
                </div>

                {{-- USER --}}
                <div class="relative group p-2">
                    <div class="flex justify-center items-center">
                        <!-- <x-heroicon-c-user class="w-7 h-7 text-gray-500 group-hover:text-primary-500"/> -->
                        @auth
                            <div class="relative cursor-pointer text-xs bg-gray-100  w-10 h-10 rounded-full flex justify-center items-center">
                                <span class="text-base font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                <div class="absolute w-2 h-2 rounded-full bg-green-500 bottom-[2px] right-[5px] "></div>
                            </div>
                        @endauth              
                        @guest
                            <h2 class="text-xs">Guest</h2>   
                        @endguest            
                    </div>

                    <div class="z-10 min-w-32 absolute top-14 -right-3 bg-white border border-gray-200 p-4 group-hover:block hidden">
                        <ul class="space-y-2">
                            @auth 
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="hover:text-primary-500">Log out</button>
                                    </form>
                                </li>
                            @endauth
                            @guest
                                <li>
                                    <a href="{{route('login')}}" class="inline-block w-full hover:text-primary-500">Login</a>
                                </li> 
                                <li>
                                    <a href="{{route('signup')}}" class="inline-block w-full hover:text-primary-500">Sign up</a>
                                </li> 
                            @endguest
                        </ul>
                    </div>
                </div>

                <x-heroicon-m-bars-3-bottom-right data-action="click->responsive-nav-menu#toggle" class="block md:hidden -translate-x-1 w-7 h-7 text-gray-500 hover:text-primary-500"/>
                
                {{-- HIDDEN CONTAINER --}}
                <div data-responsive-nav-menu-target="menu" class="z-50 translate-x-full transition-transform duration-500 p-6 bg-gray-50 fixed top-0 right-0 left-0 bottom-0">
                    <div class="flex flex-col justify-between items-center relative h-full">
                        <x-heroicon-o-x-mark data-action="click->responsive-nav-menu#toggle" class="w-7 h-7 text-gray-500 hover:text-primary-500 absolute top-0 right-0"/>
                        
                        <nav class="w-full py-12 flex flex-col justify-center items-center gap-5">
                            <ul class="w-full *:block *:border-b *:border-b-gray-300 *:w-full">
                                <x-nav.link href="/categories" :active="request()->is('categories')" >Categories</x-nav.link>
                                <x-nav.link href="/contact" :active="request()->is('contact')" >Contact</x-nav.link>
                                @guest                    
                                    <x-nav.link href="{{route('login')}}" >Login</x-nav.link>
                                    <x-nav.link href="{{route('signup')}}" >Sign up</x-nav.link>
                                @endguest
                                @auth 
                                    <li class="p-3">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="hover:text-primary-500">Log out</button>
                                        </form>
                                    </li>
                                @endauth
                            </ul>
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
                        </div>
                    </div>
                </div>
            </div> 
        </div>

            
    </div>
</header>
