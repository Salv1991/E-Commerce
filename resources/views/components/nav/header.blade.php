<header 
    class="sticky top-0 border-b border-b-stone-300 w-full z-40  bg-white" 
    data-controller="responsive-nav-menu" 
    data-action="mouseover->responsive-nav-menu#closeSubmenu">

    <div class="relative px-5 max-w-screen-xl m-auto flex justify-between items-center gap-10">

        {{-- MESSAGE --}}
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
        
        {{-- LOGO --}}
        <a href="/" class="min-w-[40px]" >
            <img src="{{ asset('svg/logo.svg') }}" alt="Logo" class="w-10 h-10">
        </a>

        <div class="w-full flex justify-between items-center gap-20">
            <nav class="flex justify-center items-center gap-5">
             
                {{-- CATEGORIES --}}
                <div id="categories-wrapper" 
                    data-responsive-nav-menu-target="categoriesWrapper" 
                    class="relative flex justify-center items-center gap-5">

                    <ul class="flex justify-center items-center">          
                        @foreach ($categories as $category)
                            <li class="group" data-action="mouseover->responsive-nav-menu#openSubmenu">

                                <a  href="{{ route('category', $category) }}"
                                    class="p-6 flex justify-between items-center group-hover:text-primary-500"
                                    data-responsive-nav-menu-target="firstDepth" 
                                    data-category-id="{{ $category->id }}">  
                                    <span class="text-sm uppercase font-semibold">{{$category->title}}</span>
                                    <!-- <x-heroicon-c-chevron-down class="w-4 h-4 hover:text-white "/> -->
                                </a>
                                <div data-responsive-nav-menu-target="submenu" 
                                    class="submenu hidden fixed top-[68px] right-0 left-0 bottom-0 bg-black/70" 
                                    data-action="mouseover->responsive-nav-menu#closeSubmenu2">
                                    <div class="category-container min-h-[450px] bg-white z-50 col-span-2 grid grid-cols-5" id="categories-container">
                                        <div class="col-span-3 grid grid-cols-2 p-8 gap-1">
                                            @foreach ($category->children as $secondDepthCategory)
                                                <div class="p-4 col-span-1">
                                                    <a href="{{ route('category', $secondDepthCategory) }}"
                                                        class="block text-black text-base font-semibold uppercase border-b border-b-gray-300 pb-1">
                                                        {{ $secondDepthCategory->title }}
                                                    </a>
                                                    <div class="flex flex-col justify-start items-start text-md mt-2 space-y-2">
                                                        @foreach ($secondDepthCategory->children as $thirdDepthCategory)
                                                            <a href="{{ route('category', $thirdDepthCategory) }}" class="text-sm uppercase text-gray-600">{{ $thirdDepthCategory->title }}</a>
                                                        @endforeach
                                                    </div>
                                                </div>       
                                            @endforeach
                                        </div>
                                        <div class="col-span-2">
                                            {{-- IMAGE --}}
                                            <a href="{{ route('category', $category)}}" class="block aspect-[360/416] h-full">  
                                                <img 
                                                    src="{{ asset('storage/' . ($category->image_path ? $category->image_path : 'products/placeholder.jpg') )}}" 
                                                    alt="Product Image" 
                                                    class="w-full h-full object-cover" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </nav>

            <div class="flex justify-center items-center gap-2">
                {{-- SEARCH --}}
                <form action="{{route('search')}}" method="get" class="relative w-[70%]">
                    <input type="search" placeholder="Search" name="query" value="{{ request()->query('query') }}" class="w-full rounded-xl py-1 pl-9 pr-2 border-2 border-gray-200 outline-none text-sm placeholder:text-sm" />
                    <button type='submit' class="absolute left-2 top-[5px] mr-2 ">
                        <x-heroicon-c-magnifying-glass  class="w-6 h-6 text-gray-500 hover:text-primary-500"/>       
                    </button>
                </form>

                {{-- WISHLIST --}}
                <x-nav.icon numberContainerId="wishlist-count" href="{{ route('wishlist') }}" :number="$wishlistCount">
                    <x-heroicon-o-heart class="wishlist-icon transform transition-transform duration-500 w-6 h-6 text-gray-700 group-hover:text-primary-500"/>
                </x-nav.icon>
                
                {{-- CART --}}
                <x-nav.icon numberContainerId="cart-count" href="#" :number="25">
                    <x-heroicon-o-shopping-bag class="w-6 h-6 text-gray-600 group-hover:text-primary-500"/>
                </x-nav.icon>

                {{-- USER --}}
                <div class="relative group ml-2">
                    @auth
                        <x-nav.user-icon :isLoggedIn=true>
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </x-nav.user-icon>
                    @endauth              
                    @guest
                        <x-nav.user-icon >
                            G
                        </x-nav.user-icon> 
                    @endguest            

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
                
                {{-- RESPONSIVE MENU ICON --}}
                <div class="cursor-pointer hidden">
                    <x-heroicon-m-bars-3-bottom-right data-action="click->responsive-nav-menu#toggleResponsiveMenu" class=" -translate-x-1 w-7 h-7 text-gray-500 hover:text-primary-500"/>
                </div>
                
                {{-- HIDDEN CONTAINER --}}
                <div data-responsive-nav-menu-target="menu" class="z-50 translate-x-full transition-transform duration-500 p-6 bg-gray-50 fixed top-0 right-0 left-0 bottom-0">
                    <div class="flex flex-col justify-between items-center relative h-full">
                        <x-heroicon-o-x-mark data-action="click->responsive-nav-menu#toggleResponsiveMenu" class="w-7 h-7 text-gray-500 hover:text-primary-500 absolute top-0 right-0"/>
                        
                        <nav class="w-full py-12 flex flex-col justify-center items-center gap-5">
                            <ul class="w-full *:block *:border-b *:border-b-gray-300 *:w-full">
                                <x-nav.link href="/categories" :active="request()->is('categories')" >Categories</x-nav.link>
                                <x-nav.link href="/contact" :active="request()->is('contact')" >Contact</x-nav.link>
                                @guest                    
                                    <x-nav.link href="{{route('login.show')}}" >Login</x-nav.link>
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
