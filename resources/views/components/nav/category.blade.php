@php
    $hasChildren = $category->children->isNotEmpty();
@endphp
<li class="group/group-{{$category->id}}">
    @if ($hasChildren)       
        <div class="peer z-10 min-w-48 absolute top-0 -right-44 bg-white border  border-gray-200 p-3 group-hover/group-{{$category->id}}:block hidden">
            <ul class="space-y-2">
                @foreach ($category->children as $subcategory)
                    <x-nav.category :category="$subcategory"/>
                @endforeach 
            </ul>
        </div>
    @endif

    <a 
        href="{{route('category', $category)}}" 
        class="peer-hover:text-primary-500 flex justify-between items-center w-full hover:text-primary-500 {{request()->is('category/' . $category->id) ? 'text-primary-500' : '' }}">
        <span>{{ $category->title }}</span>
        @if ($hasChildren)       
            <x-heroicon-c-chevron-right class="text-inherit -translate-x-1 w-4 h-4 text-gray-500 group-hover/group-{{$category->id}}:text-primary-500"/>      
        @endif
    </a>
</li>