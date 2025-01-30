<div class="bg-white relative h-screen">
    <div class="flex justify-between items-stretch h-full">
      
        <aside class="z-30 w-64 fixed top-0 bottom-0 left-0 text-white bg-black">
            <div class="border-b border-gray-50/30">
                <a href="/" class="block w-full p-5 font-bold text-lg" >
                    Admin
                </a>
            </div>
            <ul class=" *:text-sm">
                <li>
                    <a href="/" class="p-5  hover:bg-gray-50/20 flex justify-between items-center">
                        <span>Products</span>
                        <x-heroicon-o-chevron-right class="w-5 h-5"/>
                    </a>
                </li>
                <li>
                    <a href="/" class="p-5  hover:bg-gray-50/20 flex justify-between items-center">
                        <span>Categories</span>
                        <x-heroicon-o-chevron-right class="w-5 h-5"/>
                    </a>
                </li>               
                <li>
                    <a href="/" class="p-5  hover:bg-gray-50/20 flex justify-between items-center">
                        <span>Orders</span>
                        <x-heroicon-o-chevron-right class="w-5 h-5"/>
                    </a>
                </li>            
            </ul>
        </aside>

        <div class=" ml-64 relative flex-1 flex flex-col justify-start">
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
                                                <a href="{{route('admin.products')}}" class="hover:bg-gray-100/80 p-3 *:text-gray-600 flex justify-start items-center gap-3">
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
