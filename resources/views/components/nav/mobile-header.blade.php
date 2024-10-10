<div class="cursor-pointer md:hidden flex justify-between items-center gap-5">
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
            <nav class="py-12 flex flex-col justify-center items-center gap-5">
                    <x-nav.link href="/" :active="request()->is('/')" >Home</x-nav.link>
                    <x-nav.link href="/products" :active="request()->is('products')" >Products</x-nav.link>
                    <x-nav.link href="/contact" :active="request()->is('contact')" >Contact</x-nav.link>
                    <a href="#" class="hover:text-primary-500">Log in</a>
                    <a href="#" class="hover:text-primary-500">Settings</a>
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