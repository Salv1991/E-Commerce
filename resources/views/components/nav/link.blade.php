
@props(['active' => false])

<li class="p-3">
    <a {{ $attributes }} class="block {{ $active ? 'text-red-600' : 'text-black'  }} ">{{ $slot }}</a>     
</li>