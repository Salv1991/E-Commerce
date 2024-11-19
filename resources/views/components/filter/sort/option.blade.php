<a {{ $attributes }} class="flex justify-start items-center">
    <span class="mr-2 relative flex items-center justify-center w-4 h-4 rounded-full border 
        {{ $condition ? 'border-primary-500 bg-primary-500' : 'border-gray-400' }}">
        @if ( $condition )
            <span class="absolute inset-0 rounded-full border-2 border-white"></span>
        @endif
    </span>
    <span>{{ $title }}</span>
</a>