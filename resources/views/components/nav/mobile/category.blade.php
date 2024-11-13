<div class="group w-full h-12 flex items-center border-b border-b-gray-200 *:px-4">
    <a  
        href="{{ route('category', $category) }}"
        class="w-full flex justify-between items-center group-hover:bg-gray-100 py-4"
        data-category-id="{{ $category->id }}">  
        <span class="text-sm uppercase font-semibold">{{$category->title}}</span>
    </a>
    @if ($category->children?->isNotEmpty())
        <button 
            data-action="click->responsive-nav-menu#{{ $actionName }}" 
            class="border-l border-l-gray-200 h-12 w-12 hover:bg-black hover:text-white {{ $category->children->isNotEmpty() ? '' : 'hidden' }}">
            <x-heroicon-c-chevron-right class="m-auto w-5 h-5 hover:text-white "/>
        </button> 
    @endif 
</div> 