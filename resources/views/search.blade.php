<x-layout>
    <div class="w-full mt-12 px-5 pb-20 max-w-screen-xl m-auto"  data-controller="filter">

        <h1 class="text-2xl">Search results for '{{ $query }}':</h1>

        <button data-action="click->filter#toggleFilters" class="group mt-14 lg:hidden *:inline-block *:text-gray-600 flex justify-start items-center gap-1">
            <x-heroicon-m-bars-3-bottom-left  class="w-7 h-7 group-hover:text-primary-500"/>
            <span class="font-bold group-hover:text-primary-500">Filters</span>
        </button>

        <div class="products-container mt-0 lg:mt-14" > 
            <div class="relative grid grid-cols-4 mt-4">

                {{-- FILTERS --}}
                <div 
                    data-filter-target="filterContainer" 
                    data-action="click->filter#toggleFilters" 
                    class="z-10 bg-black/60 lg:bg-transparent transition-opacity duration-0 
                    fixed opacity-0 lg:opacity-100 lg:static top-[88px] right-0 bottom-0 left-0 pointer-events-none lg:pointer-events-auto">
                    <x-filter.aside/>
                </div>

                {{-- PRODUCTS --}}
                <div data-controller="wishlist" 
                    data-filter-target="productsContainer" 
                    class="col-span-4 lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 ">
                    
                    @if ($products->isNotEmpty())            
                        @foreach ($products as $product)
                            <x-product.teaser :isWishlisted="$wishlistedProductsIds->contains($product->id)" :product="$product" />
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