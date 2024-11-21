<x-layout>
    <div class="w-full mt-12 px-5 pb-20 max-w-screen-xl m-auto">

        <h1 class="text-2xl">Search results for '{{ $query }}':</h1>

        <div class="products-container mt-0 lg:mt-14"> 
            <div class="relative grid grid-cols-4 mt-4">
                {{-- FILTERS --}}
                <!-- <div data-filter-target="filterContainer" data-action="click->filter#toggleFilters" class="z-10 bg-black/60 lg:bg-transparent transition-opacity duration-0 
                    fixed opacity-0 lg:opacity-100 lg:static top-[88px] right-0 bottom-0 left-0 pointer-events-none lg:pointer-events-auto">

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
                                    title="Alphabetically Asc"/>
                                
                                <x-filter.sort.option 
                                    href="{{ request()->fullUrlWithQuery(['sort' => 'desc', 'price' => null]) }}" 
                                    :condition="request()->input('sort') === 'desc'" 
                                    title="Alphabetically Desc"/>
                                
                                <x-filter.sort.option 
                                    href="{{ request()->fullUrlWithQuery(['price' => 'asc', 'sort' => null]) }}" 
                                    :condition="request()->input('price') === 'asc'" 
                                    title="Price Asc"/>
                                    
                                <x-filter.sort.option 
                                    href="{{ request()->fullUrlWithQuery(['price' => 'desc', 'sort' => null]) }}" 
                                    :condition="request()->input('price') === 'desc'" 
                                    title="Price Desc"/>                          
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
                                    href="{{ request()->fullUrlWithQuery(['min_price_range' => null, 'max_price_range' => null ]) }}" 
                                    :condition="!request()->has('min_price_range') && !request()->has('max_price_range')" 
                                    title="Show all"/> 

                                <x-filter.sort.option 
                                    href="{{ request()->fullUrlWithQuery(['min_price_range' => '0', 'max_price_range' => '100']) }}" 
                                    :condition="request()->input('min_price_range') == '0' && request()->input('max_price_range') == '100'" 
                                    title="0-100"/> 

                                <x-filter.sort.option 
                                    href="{{ request()->fullUrlWithQuery(['min_price_range' => '101', 'max_price_range' => '300']) }}" 
                                    :condition="request()->input('min_price_range') == '101' && request()->input('max_price_range') == '300'" 
                                    title="101-300"/>

                                <x-filter.sort.option 
                                    href="{{ request()->fullUrlWithQuery(['min_price_range' => '301', 'max_price_range' => '1000']) }}" 
                                    :condition="request()->input('min_price_range') == '301' && request()->input('max_price_range') == '1000'" 
                                    title="301-1000"/>                        
                            </x-filter.sort.index>
                        </div>
                    </aside>
                </div> -->
                <div 
                    data-filter-target="filterContainer" 
                    data-action="click->filter#toggleFilters" 
                    class="z-10 bg-black/60 lg:bg-transparent transition-opacity duration-0 
                    fixed opacity-0 lg:opacity-100 lg:static top-[88px] right-0 bottom-0 left-0 pointer-events-none lg:pointer-events-auto">
                    <x-filter.aside/>
                </div>

                {{-- PRODUCTS --}}
                <div class="col-span-4 lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 ">
                    @if ($products->isNotEmpty())            
                        @foreach ($products as $product)
                            <x-product.teaser :isWishlisted="in_array($product->id, $wishlistedProductsIds)" :product="$product" />
                        @endforeach
                    @else
                        <p class="text-center w-full col-span-full mt-20 text-lg font-semibold">No products found.</p>
                    @endif
                </div>
            </div> 
        </div>

        {{-- PAGINATION --}}
        <div class="mt-20">
            {{ $products->links() }}
        </div>

    </div>
</x-layout>