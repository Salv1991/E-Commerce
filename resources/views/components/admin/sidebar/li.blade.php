<li>
    <a 
        data-action="click->sidebar#openSubmenu" 
        data-sidebar-target="button" 
        data-products 
        class="cursor-pointer p-5 {{$isRouteActive ? 'border-b border-red-800' : ''}} 
        hover:bg-gray-50/20 flex justify-between items-start">
        <div class="flex justify-between items-center gap-2 ">
            <x-dynamic-component :component="$icon" class="w-5 h-5" />
            <span>{{ $title }}</span>
        </div>
        <x-heroicon-o-chevron-right data-close class="w-5 h-5 {{$isRouteActive ? 'hidden' : ''}}"/>
        <x-heroicon-o-chevron-down data-open class="w-5 h-5 {{$isRouteActive ? '' : 'hidden'}}"/>
    </a>
    <ul data-submenu 
        class="flex flex-col justify-between items-center overflow-hidden 
        transition-transform duration-300 bg-gray-50/5 
        {{$isRouteActive ? '' : 'scale-y-0 hidden'}} origin-top">
       {{ $slot }}
    </ul>
</li>