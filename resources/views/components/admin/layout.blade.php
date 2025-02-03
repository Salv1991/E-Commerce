<div class="bg-gray-100 relative h-screen">
    <div class="flex justify-between items-stretch h-full" data-controller="sidebar">
      
    <aside>
        <div class="z-50 w-64 fixed top-0 bottom-0 left-0 text-white bg-black transition-all duration-300" data-sidebar-target="sidebar">
            <div class="border-b border-gray-50/30">
                <div class="border-b border-gray-50/30 h-[68px]"></div>    
            </div>
            <div class="p-4 bg-black hover:bg-gray-100/10 cursor-pointer" data-action="click->sidebar#toggle">
                <x-heroicon-o-arrows-right-left class="w-5 h-5 m-auto text-white"/>
            </div>
            <ul class=" *:text-sm">
                <li>
                    <a 
                        data-action="click->sidebar#openSubmenu" 
                        data-sidebar-target="button" 
                        data-products 
                        class="p-5 {{request()->routeIs('admin.product.*') ? 'border-b border-red-800' : 'hover:bg-gray-50/20'}} flex justify-between items-start">
                        <span>Product</span>
                        <x-heroicon-o-chevron-right data-close class="w-5 h-5 hidden"/>
                        <x-heroicon-o-chevron-down data-open class="w-5 h-5"/>
                    </a>
                    <ul data-submenu class="flex flex-col justify-between items-center overflow-hidden transition-transform duration-300 bg-gray-50/5 {{request()->routeIs('admin.product.*') ? '' : 'scale-y-0 hidden'}} origin-top">
                        <li class="text-xs w-full hover:bg-gray-50/20"><a href="{{route('admin.product.index')}}" class="px-10 py-4 block">Product list</a></li>
                        <li class="text-xs w-full hover:bg-gray-50/20"><a href="{{route('admin.product.create')}}" class="px-10 py-4 block">Create</a></li>
                    </ul>
                </li>
                <li>
                    <a 
                        data-action="click->sidebar#openSubmenu" 
                        data-sidebar-target="button" 
                        data-products 
                        class="p-5 {{request()->routeIs('admin.category.*') ? 'bg-gray-50/20' : 'hover:bg-gray-50/20'}} flex justify-between items-start">
                        <span>Category</span>
                        <x-heroicon-o-chevron-right data-close class="w-5 h-5"/>
                        <x-heroicon-o-chevron-down data-open class="w-5 h-5 hidden"/>
                    </a>
                    <ul data-submenu class="flex flex-col justify-between items-center overflow-hidden transition-transform duration-300 bg-gray-50/5 {{request()->routeIs('admin.category.*') ? '' : 'scale-y-0 hidden'}} origin-top">
                        <li class="text-xs w-full hover:bg-gray-50/20"><a href="/" class="px-10 py-5 block">Category list</a></li>
                        <li class="text-xs w-full hover:bg-gray-50/20"><a href="/" class="px-10 py-5 block">Create</a></li>
                    </ul>
                </li>
     
            </ul>
        </div>
        <div class="-translate-x-full z-[60] w-16 fixed top-0 bottom-0 left-0 text-white bg-black transition-all duration-300" data-sidebar-target="sidebarSmall">
            <div class="border-b border-gray-50/30">
                <div class="border-b border-gray-50/30 h-[68px]"></div>    
            </div>   
            <div class="p-4 bg-black hover:bg-gray-100/10 cursor-pointer" data-action="click->sidebar#toggle">
                <x-heroicon-o-arrows-right-left class="w-5 h-5 m-auto text-white"/>
            </div>   
        </div>
    </aside>

        <div class="ml-64 relative flex-1 flex flex-col justify-start transition-all duration-300" data-sidebar-target="content">
            <div class="shadow-md drop-shadow-xl fixed top-0 right-0 left-0 bg-black min-h-[68px] h-[68px] px-5">
                <div class="relative w-full flex justify-end items-center h-full">
                    
                   <x-error-message/>
            
                    <div class="relative group ml-2">
                        <x-nav.user-icon :isLoggedIn=true>
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </x-nav.user-icon>           

                        <div class="z-10 min-w-52 absolute top-10 -right-[35px] md:-right-3 py-[24px] group-hover:block hidden">
                            <div class="bg-white border border-gray-200">
                                <ul class="divide-y-2">
                                    <li class="">
                                        <ul class="flex flex-col">
                                            <li>
                                                <a href="{{route('admin.product.index')}}" class="hover:bg-gray-100/80 p-3 *:text-gray-600 flex justify-start items-center gap-3">
                                                    <x-heroicon-c-user class="w-6"/>
                                                    <span class="font-semibold">Admin</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

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
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-1 bg-gray-100 px-5 py-10 mt-[68px]">
               <h1 class="text-start text-2xl xs:text-2xl md:text-2xl font-semibold">{{ $title }}</h1>
               {{ $slot }}
            </div>
        </div>
    </div>
</div>
