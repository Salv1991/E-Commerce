<header 
    class="sticky top-0 border-b border-b-stone-300 w-full z-40 h-[89px] bg-white overflow-x-clip" 
    data-controller="responsive-nav-menu" 
    data-action="mouseover->responsive-nav-menu#closeSubmenu">

    <div class="relative px-5 max-w-screen-xl m-auto flex justify-between items-center gap-2 md:gap-10">

        {{-- MESSAGES --}}
        @if(session('success') || session('error'))    
            <div id='flash-message-container' class="z-30 px-5 w-full xs:w-fit text-white text-sm xs:text-base 
                text-center absolute -bottom-20 m-auto right-0 translate-x-full transform duration-300 opacity-0">
                <div id='flash-message' class="{{session('success') ? 'bg-black' : 'bg-red-400'}} p-5 rounded-lg">
                    {{ session('success') ?? session('error') }}
                </div>
            </div>

            <script>
                const flashMessage = document.getElementById('flash-message-container');

                if (flashMessage) {
                    setTimeout( () => {
                        flashMessage.classList.toggle('translate-x-full');
                        flashMessage.classList.toggle('opacity-0');
                    }, 100);

                    setTimeout( () => {
                        flashMessage.classList.toggle('translate-x-full');
                        flashMessage.classList.toggle('opacity-0');
                    }, 3000);
                }
            </script>
        @endif
        
        <div id='error-message-container' class="px-5 w-full xs:w-fit text-white text-sm xs:text-base  
        text-center absolute -bottom-20 m-auto right-0 translate-x-full transform duration-300 opacity-0">
            <div id='error-message' class="bg-red-400 p-5 rounded-lg"></div>
        </div>


        {{-- LOGO --}}
        <a href="/" class="min-w-[89px]">
            <img src="{{ asset('svg/logo7.png') }}" alt="Logo" class="w-32 overflow-hidden">
        </a>

        <div class="w-full flex justify-between items-center gap-6">
            <nav class="flex justify-center items-center gap-5">
             
                {{-- CATEGORIES --}}
                <div id="categories-wrapper" 
                    data-responsive-nav-menu-target="categoriesWrapper" 
                    class="relative justify-center items-center gap-5 hidden md:flex">

                    <ul class="flex justify-center items-center">     
                        @foreach ($categories as $category)
                            <li class="group" data-action="mouseover->responsive-nav-menu#openSubmenu">

                                <a  href="{{ route('category', $category) }}"
                                    class="px-5 py-9 group-hover:text-red-400 text-sm uppercase font-semibold"
                                    data-responsive-nav-menu-target="firstDepth" 
                                    data-category-id="{{ $category->id }}">  
                                        {{$category->title}}
                                </a>
                                     
                                <div data-responsive-nav-menu-target="submenu" 
                                    class="submenu hidden fixed top-[88px] right-0 left-0 bottom-0 bg-black/60" 
                                    data-action="mouseover->responsive-nav-menu#closeSubmenu2">
                                    <div class="category-container bg-white z-50" >
                                        <div class="max-w-screen-xl min-h-[400px] m-auto col-span-2 grid grid-cols-5" id="categories-container">
                                            <div class="col-span-3 grid grid-cols-2 xl:grid-cols-3 p-8 gap-1">
                                                @foreach ($category->children as $secondDepthCategory)
                                                    <div class="p-4 col-span-1">
                                                        <a href="{{ route('category', $secondDepthCategory) }}"
                                                            class="block text-black text-base font-semibold uppercase border-b border-b-gray-300 pb-1">
                                                            {{ $secondDepthCategory->title }}
                                                        </a>
                                                        <div class="flex flex-col justify-start items-start text-md mt-2 space-y-2">
                                                            @foreach ($secondDepthCategory->children as $thirdDepthCategory)
                                                                <a href="{{ route('category', $thirdDepthCategory) }}" class="text-sm uppercase text-gray-600">
                                                                    {{ $thirdDepthCategory->title }}</a>
                                                            @endforeach
                                                        </div>
                                                    </div>       
                                                @endforeach
                                            </div>
                                            <div class="col-span-2">
                                                {{-- IMAGE --}}
                                                <a href="{{ route('category', $category)}}" class="block aspect-[350/416] max-h-[500px] ml-auto h-full">  
                                                    <img 
                                                        src="{{ asset('storage/' . ( $category->image_path ?: 'products/placeholder.jpg') )}}" 
                                                        alt="Product Image" 
                                                        class="w-full h-full object-cover" 
                                                        loading="lazy"/>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </nav>

            <div class="flex justify-center items-center gap-2 py-6">
                {{-- SEARCH --}}
                <form action="{{route('search')}}" method="get" class="relative w-[70%] hidden xs:block">
                    <input type="search" placeholder="Search" name="query" value="{{ request()->query('query') }}" class="w-full rounded-xl py-1 pl-9 pr-2 border-2 border-gray-200 outline-none text-sm placeholder:text-sm" />
                    <button type='submit' class="absolute left-2 top-[5px] mr-2 ">
                        <x-heroicon-c-magnifying-glass  class="w-6 h-6 text-gray-500 hover:text-red-400"/>       
                    </button>
                </form>

                {{-- WISHLIST --}}
                <x-nav.icon numberContainerId="wishlist-count" href="{{ route('wishlist') }}" :number="$wishlistCount">
                    <x-heroicon-o-heart class="wishlist-icon transform transition-transform duration-300 w-6 h-6 text-gray-700"/>
                </x-nav.icon>
                
                {{-- CART --}}
                <div class="relative group" data-controller="cart">
                    <x-nav.icon numberContainerId="cart-count" href="{{ route('cart') }}" :number="$cartCount">
                        <x-heroicon-o-shopping-bag class="w-6 h-6 text-gray-700"/>
                    </x-nav.icon>
       
                    <div class="bg-white z-10 min-w-96 absolute top-10 -right-[92px] md:-right-3 pt-[24px] {{ $isCartView ? '' : 'group-hover:block'}} hidden">
                        <div class="border border-gray-200 border-b-0 px-5 py-3">
                            <div>
                                <span class="font-semibold">CART</span>
                            </div>
                        </div>

                        <div class="border border-gray-200 p-4 h-72 overflow-y-auto">
                            <ul id="cart-teasers-container" class="cart-teasers-container h-full flex flex-col *:pb-4">
                                @if ($cart->isNotEmpty())
                                    @foreach ($cart as $lineItem)
                                        <x-nav.cart-teaser :product="$lineItem->product" :quantity="$lineItem->quantity" />
                                    @endforeach
                                @endif

                                <div class="{{$cart->isNotEmpty() ? 'hidden' : 'flex'}} empty-cart-message bg-white h-full flex justify-center items-center">
                                    No products in cart.
                                </div> 
                            </ul>
                        </div>
                        
                        <div class="border border-gray-200 border-t-0 p-5 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold">Total:</span>
                                <span class="cart-total">{{number_format($cartSubtotal, 2)}}$</span>
                            </div>
                            <a href="{{ route('cart') }}" 
                                class="mt-2 inline-block text-center bg-black border-2 border-black hover:bg-white 
                                hover:text-black duration-300 px-3 py-5 text-white w-full">
                                Proceed to Cart
                            </a>             
                        </div>
                    </div>
                </div>

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

                    <div class="z-10 min-w-52 absolute top-10 -right-[35px] md:-right-3 py-[24px] group-hover:block hidden">
                        <div class="bg-white border border-gray-200">
                            <ul class="divide-y-2">
                                @auth 
                                    <li class="">
                                        <ul class="flex flex-col">
                                            <li>
                                                <a href="{{route('user.orders')}}" class="hover:bg-gray-100/80 p-3 *:text-gray-600 flex justify-start items-center gap-3">
                                                    <x-heroicon-m-archive-box class="w-6"/>
                                                    <span class="font-semibold">My orders</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{route('settings.customer-information.show')}}" class="hover:bg-gray-100/80 p-3 *:text-gray-600 flex justify-start items-center gap-3">
                                                    <x-heroicon-c-cog-8-tooth class="w-6"/>
                                                    <span class="font-semibold">Settings</span>
                                                </a>
                                            </li>
                                        </ul>

                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="hover:bg-gray-100/80 p-3 *:text-gray-600 w-full flex justify-start items-center gap-2">
                                                <x-heroicon-c-arrow-right-start-on-rectangle class="w-7 h-7"/>
                                                <span class="font-semibold">Log Out</span>
                                            </button>
                                        </form>
                                    </li>                                  
                                @endauth

                                @guest
                                    <li class=" ">
                                        <ul>
                                            <li>
                                                <a href="{{route('login')}}" class="hover:bg-gray-100/80 p-3 *:text-gray-600 flex justify-start items-center gap-3">   
                                                    <x-heroicon-c-arrow-right-end-on-rectangle class="w-7 h-7"/>
                                                    <span class="font-semibold">Log in</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{route('signup')}}" class="hover:bg-gray-100/80 p-3 *:text-gray-600 flex justify-start items-center gap-3">   
                                                    <x-heroicon-c-user class="w-7 h-7 translate-x-[3px]"/>
                                                    <span class="font-semibold">Sign up</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li> 
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>
                
                {{-- RESPONSIVE MENU BUTTON --}}
                <div class="cursor-pointer block md:hidden">
                    <x-heroicon-m-bars-3-bottom-right data-action="click->responsive-nav-menu#toggleResponsiveMenu" class="w-7 h-7 text-gray-500 hover:text-red-400"/>
                </div>
                
                {{-- HIDDEN CATEGORIES CONTAINER --}}
                <x-nav.mobile.responsive-menu :$categories/>

            </div> 
        </div>     
    </div>
</header>
