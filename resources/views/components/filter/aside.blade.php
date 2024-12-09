<aside data-filter-target="aside"
    class="z-10 overflow-y-auto border-r-2 lg:border-none border-r-gray-200  lg:static 
        transition-transform duration-500 w-72 h-full -translate-x-full lg:-translate-x-0
    bg-white lg:bg-transparent lg:col-span-1 lg:w-full pl-5 lg:pl-0 py-5 lg:py-0 pr-5">

    <div class="block lg:hidden ml-auto w-fit">
        <button data-filter-target="closeButton" 
            data-action="click->filter#toggleFilters">
            <x-heroicon-o-x-mark  class="w-5 h-5 text-black hover:text-primary-500 "/>
        </button>
    </div>

    <h2 class="font-bold">Filters</h2>

    <div class="flex flex-col justify-start items-center mt-2">
        <x-filter.sort.index title="SORT BY">
            <x-filter.sort.option 
                href="{{ request()->fullUrlWithQuery(['sort' => 'asc', 'price' => null]) }}" 
                :condition="request()->input('sort') === 'asc'" 
                title="Name (A-Z)"/>
            
            <x-filter.sort.option 
                href="{{ request()->fullUrlWithQuery(['sort' => 'desc', 'price' => null]) }}" 
                :condition="request()->input('sort') === 'desc'" 
                title="Name (Z-A)"/>
            
            <x-filter.sort.option 
                href="{{ request()->fullUrlWithQuery(['price' => 'asc', 'sort' => null]) }}" 
                :condition="request()->input('price') === 'asc'" 
                title="Price (Low-High)"/>
                
            <x-filter.sort.option 
                href="{{ request()->fullUrlWithQuery(['price' => 'desc', 'sort' => null]) }}" 
                :condition="request()->input('price') === 'desc'" 
                title="Price (High-Low)"/>                          
        </x-filter.sort.index> 
        
        <x-filter.sort.index title="DISCOUNTS">                        
            <x-filter.sort.option 
                href="{{ request()->fullUrlWithQuery(['discounted_products' => null]) }}" 
                :condition="!request()->has('discounted_products')" 
                title="Show all"/> 
                
            <x-filter.sort.option 
                href="{{ request()->fullUrlWithQuery(['discounted_products' => true, 'page'=> null ]) }}" 
                :condition="request()->input('discounted_products') == true" 
                title="Show only discounts"/>                           
        </x-filter.sort.index>

        <x-filter.sort.index title="PRICE RANGE">
            <x-filter.sort.option 
                href="{{ request()->fullUrlWithQuery(['min_price_range' => null, 'max_price_range' => null , 'page' => null ]) }}" 
                :condition="!request()->has('min_price_range') && !request()->has('max_price_range')" 
                title="Show all"/> 

            <x-filter.sort.option 
                href="{{ request()->fullUrlWithQuery(['min_price_range' => '0', 'max_price_range' => '100', 'page' => null ]) }}" 
                :condition="request()->input('min_price_range') == '0' && request()->input('max_price_range') == '100'" 
                title="0-100"/> 

            <x-filter.sort.option 
                href="{{ request()->fullUrlWithQuery(['min_price_range' => '101', 'max_price_range' => '300', 'page' => null ]) }}" 
                :condition="request()->input('min_price_range') == '101' && request()->input('max_price_range') == '300'" 
                title="101-300"/>

            <x-filter.sort.option 
                href="{{ request()->fullUrlWithQuery(['min_price_range' => '301', 'max_price_range' => '1000', 'page' => null ]) }}" 
                :condition="request()->input('min_price_range') == '301' && request()->input('max_price_range') == '1000'" 
                title="301-1000"/>                        
        </x-filter.sort.index>
    </div>
</aside>
