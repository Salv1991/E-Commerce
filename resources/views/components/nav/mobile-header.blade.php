<div class="cursor-pointer md:hidden flex justify-between items-center gap-5 z-50">
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

    <x-heroicon-m-bars-3-bottom-right data-action="click->responsive-nav-menu#toggle" class="-translate-x-1 w-6 h-6 text-gray-500 hover:text-primary-500"/>

    {{-- HIDDEN CONTAINER --}}
    <div data-responsive-nav-menu-target="menu" class="translate-x-full transition-transform duration-500 p-6 bg-gray-50 fixed top-0 right-0 left-0 bottom-0">
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