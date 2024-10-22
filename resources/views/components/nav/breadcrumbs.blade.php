<div class="flex justify-start items-center gap-2 text-gray-400 mt-12 mb-5"> 
    <a href="/" class="text-black hover:bg-gray-50 rounded-full p-2">
        <x-heroicon-o-home class="w-5 h-5 text-primary-500"/>
    </a>

    <div>
        <x-heroicon-c-chevron-right class="w-4 h-4 text-gray-400"/>
    </div> 

    <div>     
        <a href="{{ route('category', ['category' => $category->id]) }}" 
        class="{{ request()->routeIs('category', ['category' => $category->id]) ? 'text-black' : 'text-gray-400' }}">
            {{ $category?->title }}
        </a>          
    </div>

    @if (isset($product) && $product)     
        <div>
            <x-heroicon-c-chevron-right class="w-4 h-4 text-gray-400"/>
        </div>
        <div>
            <a>{{ $product->title }}</a>
        </div>
    @endif
</div>

<!-- OLA ta categories tou proiontos se foreach kai sto telos an iparxei proion to proion me 
 allo xrwma kai xwris link. an de iparxei proion tote 
 itteration->last xoris link i psaxnw to url an iparxei to 
 sigkekrimeno category nai kalitero auto. -->