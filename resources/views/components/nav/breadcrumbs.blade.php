@php
    $breadcrumbCategories = collect();
    $current = $category;

    while($current) {
        $breadcrumbCategories->prepend($current);
        $current = $current->parent;
    }
@endphp

<div class="flex justify-start items-center gap-2 flex-wrap text-gray-400 mt-3 mb-5"> 
    <a href="/" class="text-black hover:bg-gray-50 rounded-full p-2">
        <x-heroicon-o-home class="w-5 h-5 text-primary-500"/>
    </a>

    <div>
        <x-heroicon-c-chevron-right class="w-4 h-4 text-black"/>
    </div> 
    
    @foreach ($breadcrumbCategories as $category)
        <div>     
            @if ($loop->last && !isset($product))
                <span class="text-gray-400">{{ $category->title }}</span>
            @else
                <a 
                    href="{{ route('category', ['category' => $category->id]) }}" 
                    class="text-black hover:underline">
                    {{ $category->title }}
                </a>          
            @endif
        </div> 
        @if (!$loop->last)
            <div>
                <x-heroicon-c-chevron-right class="w-4 h-4 {{ $loop->last ? 'text-gray-400' : 'text-black' }}"/>
            </div> 
        @endif 
    @endforeach
    
    @if (isset($product) && $product)     
        <div>
            <x-heroicon-c-chevron-right class="w-4 h-4 text-black"/>
        </div>
        <div>
            <p class="text-gray-400">{{ $product->title }}</p>
        </div>
    @endif
</div>
