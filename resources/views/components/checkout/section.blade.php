<section {{ $attributes->merge(['class' => '']) }}>
    <h2 class="text-xl font-semibold border-b border-black pb-2 mb-5">{{ $title }}</h2>  
   {{ $slot }}
</section>