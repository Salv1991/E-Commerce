<fieldset class="flex flex-col justify-start items-start w-full my-2">
    <legend class="z-20 pt-4 pb-2 w-full border-t border-gray-400 font-bold text-sm flex justify-between items-center">
        <span class="tracking-[1px] text-sm">{{ $title }}</span>
        <x-heroicon-c-chevron-down class="w-6 h-6 text-black"/>
    </legend>
    <div data-filter-target="filterOptionsContainer" class="z-10 block w-full *:text-start *:py-1">
        {{ $slot }}
    </div>
</fieldset>

