
@props(['active' => false])

<li class="p-2">
    <a {{ $attributes }} class="block {{ $active ? 'text-primary-500' : 'text-gray-600'  }} font-semibold">
        {{ $slot }}
    </a>     
</li>

