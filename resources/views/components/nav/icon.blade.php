<a {{ $attributes }} class="relative group p-2 w-10 h-10 rounded-full flex justify-center items-center">
    <div id="{{$numberContainerId}}" class="absolute -top-2 -right-1 bg-gray-600 text-white font-bold
    rounded-full w-5 h-5 text-[10px] flex justify-center items-center group-hover:bg-primary-500 border border-white">
        {{ $number }}
    </div>
    {{ $slot }}
</a>