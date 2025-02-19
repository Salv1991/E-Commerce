<x-layout> 
    <div class="w-full px-5 pb-5 max-w-screen-xl m-auto" data-controller="filter" >
        
        {{-- BREADCRUMBS --}}
        <x-nav.breadcrumbs :$category />

        <h1 class="font-semibold text-3xl text-center mt-10 uppercase text-gray-700">{{ $category->title }}</h1>

        <p class="text-center mt-6 text-gray-500">{{ $category->description}}</p>

        <button 
            data-action="click->filter#toggleFilters" 
            class="group mt-14 lg:hidden *:inline-block *:text-gray-600 
            flex justify-start items-center gap-1">
            <x-heroicon-m-bars-3-bottom-left class="w-7 h-7 group-hover:text-primary-500"/>
            <span class="font-bold group-hover:text-primary-500">
                Filters
            </span>
        </button>

        <div class="products-container mt-0 lg:mt-14"> 
            <div class="relative grid grid-cols-4 mt-4">

                {{-- FILTERS --}}
                <div 
                    data-filter-target="filterContainer" 
                    data-action="click->filter#toggleFilters" 
                    class="z-10 bg-black/60 lg:bg-transparent transition-opacity duration-0 fixed opacity-0 lg:opacity-100 
                    lg:static top-[88px] right-0 bottom-0 left-0 pointer-events-none lg:pointer-events-auto">
                    <x-filter.aside/>
                </div>
                
                {{-- PRODUCTS --}}
                <div 
                    data-controller="wishlist cart" 
                    data-filter-target="productsContainer" 
                    class="col-span-4 lg:col-span-3 grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 gap-10 ">
                    @if ($products->isNotEmpty())            
                        @foreach ($products as $product)
                            <x-product.teaser 
                                :isWishlisted="in_array($product->id, $wishlistedProductsIds)" 
                                :product="$product" />
                        @endforeach
                    @else
                        <div class="col-span-full bg-gray-50 border border-gray-200 h-96 lg:h-[600px] px-5 py-5 flex justify-center items-center text-gray-500">
                            No products found.
                        </div>
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