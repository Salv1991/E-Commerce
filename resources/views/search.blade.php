<x-layout>
    <div class="w-full mt-12 px-5 pb-20 max-w-screen-xl m-auto">

        <h1 class="text-2xl">Search results for '{{ $query }}':</h1>

        {{-- PRODUCTS --}}
        <div class="products-container mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                @if ($products->isNotEmpty())     
                    @foreach ($products as $product)
                        <x-product.teaser :product="$product" />
                    @endforeach
                @else
                    No products found.
                @endif
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-20">
            {{ $products->links() }}
        </div>

    </div>
</x-layout>