<div class="bg-gray-100 relative h-screen">
    <div class="flex justify-between items-stretch h-full" data-controller="sidebar">
      
    <aside>
        <div class="z-50 w-64 fixed top-0 bottom-0 left-0 text-white bg-black transition-all duration-300" data-sidebar-target="sidebar">
            <div class="border-b border-gray-50/30">
                <a href="{{route('home')}}" class="flex justify-center items-center border-b border-gray-50/30 h-[68px] p-4 bg-black hover:bg-gray-100/10 cursor-pointer">
                    <x-heroicon-c-home class="w-5 h-5 m-auto text-white"/>
                </a>
            </div>
            <div class="p-4 bg-black hover:bg-gray-100/10 cursor-pointer" data-action="click->sidebar#toggle">
                <x-heroicon-o-arrows-right-left class="w-5 h-5 m-auto text-white"/>
            </div>
            
            <ul class="*:text-sm">
                <x-admin.sidebar.li title="Product" :isRouteActive="request()->routeIs('admin.product.*')" icon="heroicon-o-archive-box">
                    <li class="text-xs w-full hover:bg-gray-50/20"><a href="{{route('admin.product.index')}}" class="px-10 py-4 block select-none">Product list</a></li>
                    <li class="text-xs w-full hover:bg-gray-50/20"><a href="{{route('admin.product.create')}}" class="px-10 py-4 block select-none">Create</a></li>
                </x-admin.sidebar.li>

                <x-admin.sidebar.li title="Category" :isRouteActive="request()->routeIs('admin.category.*')"  icon="heroicon-o-inbox">
                    <li class="text-xs w-full hover:bg-gray-50/20"><a href="{{route('admin.category.list')}}" class="px-10 py-5 block select-none">Category list</a></li>
                    <li class="text-xs w-full hover:bg-gray-50/20"><a href="{{route('admin.category.create.show')}}" class="px-10 py-5 block select-none">Create</a></li>
                </x-admin.sidebar.li>

                <x-admin.sidebar.li title="Order" :isRouteActive="request()->routeIs('admin.order.*')"  icon="heroicon-o-credit-card">
                    <li class="text-xs w-full hover:bg-gray-50/20"><a href="{{route('admin.order.list')}}" class="px-10 py-5 block select-none">Order list</a></li>
                </x-admin.sidebar.li>
            </ul>
        </div>
        <div class="-translate-x-full z-[60] w-16 fixed top-0 bottom-0 left-0 text-white bg-black transition-all duration-300" data-sidebar-target="sidebarSmall">
            <div class="border-b border-gray-50/30">
                <a href="{{route('home')}}" class="flex justify-center items-center border-b border-gray-50/30 h-[68px] p-4 bg-black hover:bg-gray-100/10 cursor-pointer">
                    <x-heroicon-c-home class="w-5 h-5 m-auto text-white"/>
                </a>            
            </div>   
            <div class="p-4 bg-black hover:bg-gray-100/10 cursor-pointer" data-action="click->sidebar#toggle">
                <x-heroicon-o-arrows-right-left class="w-5 h-5 m-auto text-white"/>
            </div>   
        </div>
    </aside>

        <div class="pl-64 w-full relative flex-1 flex flex-col justify-start transition-all duration-300" data-sidebar-target="content">
            <div class="shadow-md drop-shadow-xl z-40 fixed top-0 right-0 left-0 bg-black min-h-[68px] h-[68px] px-5">
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

            <div class="w-full bg-gray-100 px-5 py-10 mt-[68px]">
               <h1 class="text-start text-2xl xs:text-2xl md:text-2xl font-semibold">
                    @isset ($href)
                        <a class="text-start text-2xl xs:text-2xl md:text-2xl font-semibold hover:text-red-500" href="{{ $href }}">{{ $title }}</a>              
                    @else
                        {{ $title }}
                    @endisset
                </h1>
               {{ $slot }}
            </div>
        </div>
    </div>
</div>
