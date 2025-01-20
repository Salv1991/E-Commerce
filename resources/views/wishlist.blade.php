<x-layout>
    <div class="mt-10 max-w-screen-xl m-auto p-6">
        <h1 class="text-center text-4xl font-semibold">My Wishlist</h1>

        <div id="wishlist-container"
            data-controller="wishlist cart" 
            data-filter-target="wishlistsContainer" 
            class="mt-14 flex flex-col justify-between divide-y-2 divide-gray-100 items-center bg-gray-100 p-2">

            <div class="hidden lg:grid grid-cols-8 items-center justify-center gap-5 h-20 w-full">
                <div class="grid grid-cols-2 col-span-2">
                    <div class="col-span-1"></div>
                    <div class="col-span-1"></div>
                </div>

                <div class="grid grid-cols-7 col-span-6">
                    <div class="col-span-2 font-semibold text-lg">
                        <span>Title</span>
                    </div>

                    <div class="col-span-2 font-semibold text-gray-600">
                        <span>Price</span>
                    </div>

                    <div class="col-span-1 font-semibold">
                        <span>Stock Status</span>
                    </div>   
                </div>

            </div>

            @if ($wishlistedProducts->isNotEmpty())
                @foreach ($wishlistedProducts as $product )
                    <x-wishlist.teaser :$product />
                @endforeach
                
            @else
               <span> Your Wishlist is empty ... </span>
            @endif
           
        </div>
    </div>
</x-layout>