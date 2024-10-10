@props(['active' => false])

<li>
    <a {{ $attributes }} class="{{ $active ? 'text-red-600' : 'text-black'  }} p-3">{{ $slot }}</a>     
</li>