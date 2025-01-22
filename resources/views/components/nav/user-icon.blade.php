@props(['isLoggedIn' => false])

<div class="relative cursor-pointer text-xs bg-gray-500 group-hover:bg-gray-700 w-10 h-10 rounded-lg flex justify-center items-center">
    <span class="text-base font-semibold text-white">{{ $slot }}</span>
    <div class="absolute border border-gray-100 w-3 h-3 rounded-full {{ $isLoggedIn ? 'bg-green-500' : 'bg-gray-300'}} bottom-[0px] right-[0px] "></div>
</div>