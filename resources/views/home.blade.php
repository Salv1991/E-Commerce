<x-layout>
    <div class="h-[400px] md:h-[650px] w-full overflow-hidden relative">
        <img 
            src="{{ asset('storage/home.jpg') }}" 
            alt="Product Image" 
            class="h-full w-full object-cover object-top" />
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute right-5 sm:right-8 md:right-24 lg:right-28 bottom-10 md:bottom-20 lg:bottom-20 p-3">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold text-white text-end">E-Commerce</h1>
                <p class="text-gray-50 text-right text-sm sm:text-base lg:text-xl w-[300px] md:w-[450px] lg:w-[600px] mt-4 md:mt-5">No shipping fees on purchases over $200</p>
            </div>
    </div>
   
    <div class="w-full mt-20 px-5 pb-20 max-w-screen-xl m-auto">

        {{-- CATEGORIES --}}
        @if($randomCategories->isNotEmpty())
        <h2 class="text-2xl font-semibold border-b-2 border-b-pink-400 w-fit mb-5">Categories</h2>

            <div class="grid grid-cols-2 gap-5 mb-16 ">
                <div class="col-span-full md:col-span-1 order-2 md:order-1 grid grid-cols-2 grid-rows-2 gap-5 md:max-h-[540px]">
                    @foreach ($randomCategories->take(3) as $category) 
                        <a @class([
                                'col-span-1 aspect-square md:aspect-auto' => $loop->index == 0 || $loop->index == 1 || $loop->index == 3,
                                'col-span-full aspect-[2/1] md:aspect-auto' => $loop->index == 2,
                                'h-[400px] md:h-[260px] w-full overflow-hidden relative group rounded-md h-auto w-auto md:h-full md:w-full'
                            ])
                            href="{{ route('category', $category->id) }}">
                            <img 
                                src="{{asset('storage/' . $category->image_path)}}" 
                                alt="Product Image" 
                                class="h-full w-full object-cover object-top transform transition-transform duration-500 ease-in-out group-hover:scale-105" />
                            <div class="absolute right-0 bottom-0 py-1 xs:py-2 px-4 md:px-8 bg-black/30 w-full">
                                <h1 class="text-lg xs:text-xl md:text-2xl font-semibold text-white text-end">{{ucfirst($category->title)}}</h1>
                                <p class="text-gray-50 text-right text-[11px] xs:text-xs md:text-sm mt-1 md:mt-3 line-clamp-2">{{$category->description}}</p>
                            </div>
                        </a>
                    @endforeach   
                </div>
                <div class="col-span-full md:col-span-1 order-1 md:order-2 grid grid-rows-1 md:grid-rows-2 row-span-1 md:row-span-2 md:max-h-[540px] aspect-[2/1] md:aspect-auto">
                    <a href="{{ route('category', $randomCategories->last()->id) }}" class="h-full w-full overflow-hidden rounded-md relative row-span-2 group">
                        <img 
                            src="{{asset('storage/' . $randomCategories->last()->image_path)}}" 
                            alt="Product Image" 
                            class="h-full w-full object-cover object-top rounded-md transform transition-transform duration-500 ease-in-out group-hover:scale-105" />
                        <div class="absolute right-0 bottom-0 py-1 xs:py-2 px-4 md:px-8 bg-black/30 ">
                            <h1 class="text-xl md:text-2xl font-semibold text-white text-end">{{ucfirst($randomCategories->last()->title)}}</h1>
                            <p class="text-gray-50 text-right text-xs md:text-sm  mt-1 md:mt-3 line-clamp-2">{{$randomCategories->last()->description}}</p>
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
                            :isWishlisted="in_array($product->id, $wishlistedProductsIds)" 
                            :product="$product" />
                    @endforeach
            </div>
        @endif
    </div>

</x-layout>