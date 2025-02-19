<div data-responsive-nav-menu-target="responsiveMenuContainer" 
    class="md:hidden fixed z-50 top-0 right-0 left-0 bottom-0 bg-black/60 opacity-0 transition-opacity duration-500 pointer-events-none">
    <div class="relative h-full w-full">

        {{-- CLOSE BUTTON --}}
        <button data-responsive-nav-menu-target="closeButton" 
            data-action="click->responsive-nav-menu#toggleResponsiveMenu"
            class="hidden absolute top-[26px] right-[18px]">
            <x-heroicon-o-x-mark  class="w-8 h-8 text-white hover:text-primary-500 "/>
        </button>

        {{-- RESPONSIVE MENU CARD --}}
        <div data-responsive-nav-menu-target="menu" 
            class="flex flex-col justify-between items-center h-full bg-gray-50 w-3/4 xs:w-72 -translate-x-full transition-transform duration-300">
            
            <div class="w-full flex flex-col justify-start items-center relative">
                {{-- SEARCH --}}
                <form action="{{route('search')}}" method="get" class="relative w-full">
                    <input 
                        type="search" 
                        placeholder="Search" 
                        name="query" 
                        value="{{ request()->query('query') }}" 
                        class="w-full py-3 pl-9 pr-2 border-2 border-gray-200 outline-none text-sm placeholder:text-sm" />
                    <button type='submit' class="absolute left-2 top-[12px] mr-2 ">
                        <x-heroicon-c-magnifying-glass  class="w-6 h-6 text-gray-500 hover:text-primary-500"/>       
                    </button>
                </form>

                <nav class="w-full flex flex-col justify-center items-center gap-5">
                    <ul class="w-full *:block *:w-full">
                
                        {{-- MOBILE CATEGORIES --}}
                        @foreach ($categories as $category)    
                            <li class="mobile-category flex flex-col justify-between items-center">
                                
                                {{-- FIRST DEPTH CATEGORIES --}}
                                <x-nav.mobile.category :category="$category"  actionName="toggleMobileSubmenu"/>

                                {{--SECOND DEPTH CATEGORIES CARD--}}
                                <x-nav.mobile.category-card-container class="mobile-category-submenu">
                                    {{-- CARD TITLE --}}
                                    <x-nav.mobile.category-card-title :parentCategoryTitle="$category->title" actionName="toggleMobileSubmenu"/>
                                    {{--SECOND DEPTH MOBILE CATEGORIES --}}
                                    <ul class="w-full">
                                        @foreach ($category->children as $secondDepthCategory)
                                            <li class="children-mobile-submenu flex flex-col justify-between items-center">
                                                {{-- SECOND DEPTH CATEGORY --}}
                                                <x-nav.mobile.category :category="$secondDepthCategory" actionName="toggleMobileChildrenSubmenu" />

                                                {{-- THIRD DEPTH CHILDREN CATEGORIES CARD --}}
                                                <x-nav.mobile.category-card-container class="submenu-card">
                                                    {{-- CARD TITLE --}}
                                                    <x-nav.mobile.category-card-title :parentCategoryTitle="$secondDepthCategory->title" actionName="toggleMobileChildrenSubmenu"/>
                                                    {{-- THIRD DEPTH CATEGORIES--}}
                                                    <ul class="w-full">
                                                        @foreach ($secondDepthCategory->children as $thirdDepthCategory)
                                                            <li class="w-full h-12 flex justify-between items-center border-b border-b-gray-200 hover:bg-gray-100">
                                                                <a href="{{ route('category', $thirdDepthCategory) }}" class="w-full h-full block p-4 text-black text-sm font-semibold uppercase">{{ $thirdDepthCategory->title }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </x-nav.mobile.category-card-container>  
                                            </li>   
                                        @endforeach
                                    </ul>                  
                                </x-nav.mobile.category-card-container>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
