<div {{ $attributes->merge(['class' => 'flex flex-col justify-start items-start w-72 z-50 fixed -translate-x-full transition-transform duration-500 top-0 right-0 left-0 bottom-0 *:border-b *:border-b-gray-300']) }}>
    <div class="flex flex-col justify-start items-center relative h-full bg-gray-50 w-full">    
        {{ $slot }}
    </div>
</div>