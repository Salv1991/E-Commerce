<header 
    class="sticky top-0 border-b border-b-stone-300 w-full z-40  bg-white" 
    data-controller="responsive-nav-menu" 
    data-action="mouseover->responsive-nav-menu#closeSubmenu">

    <div class="relative px-5 max-w-screen-xl m-auto flex justify-between items-center gap-2 md:gap-10">

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
                                    class="px-5 py-9 group-hover:text-primary-500 text-sm uppercase font-semibold"
                                    data-responsive-nav-menu-target="firstDepth" 
                                    data-category-id="{{ $category->id }}">  
                                        {{$category->title}}
                                </a>
                                
                                <div data-responsive-nav-menu-target="submenu" 
                                    class="submenu hidden fixed top-[88px] right-0 left-0 bottom-0 bg-black/60" 
                                    data-action="mouseover->responsive-nav-menu#closeSubmenu2">
                                    <div class="category-container  bg-white z-50 " >
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
                                                <a href="{{ route('category', $category)}}" class="block aspect-[360/416] max-h-[600px] h-full">  
                                                    <img 
                                                        src="{{ asset('storage/' . ($category->image_path ? $category->image_path : 'products/placeholder.jpg') )}}" 
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
                <!-- <x-nav.icon numberContainerId="cart-count" href="{{ route('cart.index') }}" :number="$cartCount">
                    <x-heroicon-o-shopping-bag class="w-6 h-6 text-gray-600 group-hover:text-primary-500"/>
                </x-nav.icon> -->

                <div class="relative group ">
                    <x-nav.icon numberContainerId="cart-count" href="{{ route('cart.index') }}" :number="$cartCount">
                        <x-heroicon-o-shopping-bag class="w-6 h-6 text-gray-600 group-hover:text-primary-500"/>
                    </x-nav.icon>
       
                    <div class="bg-white z-10 min-w-96 absolute top-10 -right-[92px] md:-right-3 pt-[24px] group-hover:block hidden">
                        <div class="border border-gray-200 border-b-0 px-5 py-3">
                            <div>
                                <span class="font-semibold">CART</span>
                            </div>
                        </div>
                        @if ($cart->isNotEmpty())
                            <div class="border border-gray-200 p-4 max-h-80 overflow-y-auto">
                                <ul class="flex flex-col gap-5">
                                    @foreach ($cart as $lineItem)
                                        <li class="grid grid-cols-4 gap-3">
                                            <a href="{{route('product', $lineItem->product)}}" class="col-span-1 lg:col-span-1 w-full h-full overflow-hidden aspect-[.75]">
                                                <img 
                                                    class="h-full w-full object-cover object-center" 
                                                    src="{{ asset('storage/' . ($lineItem->product->images->isNotEmpty() ? $lineItem->product->images->first()->image_path : 'products/placeholder.jpg') )}}" 
                                                    alt="Product Image">  
                                            </a>
                                            <div class="col-span-2 flex flex-col justify-between items-start">
                                                <div>
                                                    <a 
                                                        href="{{ route('product', $lineItem->product) }}" 
                                                        class="font-bold text-gray-600 hover:text-primary-500">
                                                        {{$lineItem->quantity . ' x ' . $lineItem->product->title}}
                                                    </a>
                                                    <p class="line-clamp-2 text-xs text-gray-500">{{$lineItem->product->description}}</p>
                                                </div>
                                                <span class="mt-3 inline-block font-semibold text-sm">{{ $lineItem->quantity * $lineItem->product->current_price}}$</span>                                           
                                            </div>
                                            <div class="col-span-1 justify-self-end">
                                                <form class="w-full" method='post' action="{{ route('cart.delete', $lineItem->product->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit">
                                                        <x-heroicon-o-x-mark class="inline-block w-6 h-6 text-gray-500 hover:text-primary-500"/>
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="bg-white border border-gray-200 p-4 h-40 flex justify-center items-center">
                                No products in cart.
                            </div>
                        @endif
                        <div class="border border-gray-200 border-t-0 p-5 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold">Total:</span>
                                <span>{{$cartTotal}}$</span>
                            </div>
                            <a href="{{ route('cart.index') }}" 
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

                    <div class="z-10 min-w-32 absolute top-10 -right-[35px] md:-right-3 py-[24px] group-hover:block hidden">
                        <div class="bg-white border 
                        border-gray-200 p-4">
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
                </div>
                
                {{-- RESPONSIVE MENU ICON --}}
                <div class="cursor-pointer block md:hidden">
                    <x-heroicon-m-bars-3-bottom-right data-action="click->responsive-nav-menu#toggleResponsiveMenu" class="w-7 h-7 text-gray-500 hover:text-primary-500"/>
                </div>
                
                {{-- HIDDEN CONTAINER --}}
                <x-nav.mobile.responsive-menu  :$categories/>

            </div> 
        </div>     
    </div>
</header>
