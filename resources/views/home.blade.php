<x-layout>
    <div class="h-[400px] md:h-[650px] w-full overflow-hidden relative">
        <img 
            src="{{ asset('storage/home.jpg') }}" 
            alt="Product Image" 
            class="h-full w-full object-cover object-top rounded-md" />
            <div class="absolute right-8 sm:right-8 md:right-20 bottom-16 md:bottom-28 p-3">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold text-white text-end">E-Commerce</h1>
                <p class="text-gray-50 text-right text-sm sm:text-base lg:text-xl w-[280px] md:w-[450px] lg:w-[600px] mt-4 md:mt-5">lor wd wad wad awdawd wadad adawdwadwad wadawdaw dwadawd waddaw adw wadwad.</p>
            </div>
    </div>
   
    <div class="w-full mt-20 px-5 pb-20 max-w-screen-xl m-auto">

        {{-- CATEGORIES --}}
        @if($randomCategories->isNotEmpty())
        <h2 class="text-2xl font-semibold border-b-2 border-b-pink-400 w-fit mb-5">Categories</h2>

            <div class="grid grid-cols-2 gap-5 max-h-[540px] mb-16">
                <div class="col-span-1 grid grid-cols-2 grid-rows-2 gap-5">
                    @foreach ($randomCategories->take(3) as $category) 
                        <a @class([
                                'col-span-1' => $loop->index == 0 || $loop->index == 1 || $loop->index == 3,
                                'col-span-full' => $loop->index == 2,
                                'h-[400px] md:h-[260px] w-full overflow-hidden relative'
                            ])
                            href="{{ route('category', $category->id) }}">
                            <img 
                                src="{{asset('storage/' . $category->image_path)}}" 
                                alt="Product Image" 
                                class="h-full w-full object-cover object-top rounded-md" />
                            <div class="absolute right-0 bottom-0 py-2 px-8 bg-black/30 w-full">
                                <h1 class="text-2xl font-semibold text-white text-end">{{ucfirst($category->title)}}</h1>
                                <p class="text-gray-50 text-right text-sm mt-3 line-clamp-2">{{$category->description}}</p>
                            </div>
                        </a>
                    @endforeach   
                </div>
                <div class="col-span-1 grid grid-rows-2 max-h-[540px]">
                    <a href="{{ route('category', $randomCategories->last()->id) }}" class="h-full w-full overflow-hidden relative row-span-2">
                        <img 
                            src="{{asset('storage/' . $randomCategories->last()->image_path)}}" 
                            alt="Product Image" 
                            class="h-full w-full object-cover object-top rounded-md" />
                        <div class="absolute right-0 bottom-0 py-2 px-8 bg-black/30 ">
                            <h1 class="text-2xl font-semibold text-white text-end">{{ucfirst($randomCategories->last()->title)}}</h1>
                            <p class="text-gray-50 text-right text-sm mt-3 md:mt-5">{{$randomCategories->last()->description}}</p>
                        </div>
                    </a>
                </div>
            </div>
        @endif
        {{-- PRODUCTS --}}
        @if ($latestProducts->isNotEmpty())
            <h2 class="text-2xl font-semibold border-b-2 border-b-pink-400 w-fit mb-5">Latest products</h2>
            <div 
                data-controller="wishlist cart" 
                data-filter-target="productsContainer" 
                class="col-span-4 lg:col-span-3 grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10 ">
                    @foreach ($latestProducts as $product)
                        <x-product.teaser 
                            :isWishlisted="$wishlistedProductsIds->contains($product->id)" 
                            :product="$product" />
                    @endforeach
            </div>
        @endif
    </div>

</x-layout>