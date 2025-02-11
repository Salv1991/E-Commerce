<form 
    action="{{ route('cart.add', $product->id) }}" 
    method="post"
    data-action="submit->cart#add">
    @csrf
    <button type="submit" 
        @disabled($product->stock <= 0)                              
        class="w-full p-2 rounded-sm hover:bg-black disabled:hover:bg-transparent 
        text-black disabled:text-gray-400 hover:text-white duration-300">
        <x-heroicon-c-shopping-bag class="inline-block w-6 h-6"/>
    </button>

    <button disabled data-loading-image-container class="hidden translate-x-[6px] p-2">
        <img src="{{ asset('svg/loading.svg') }}" alt="Logo" class="rotate -translate-x-[7px] mt-1 w-6 h-6 rotate-0 transition-all ease-in-out duration-[2500ms] overflow-hidden">
    </button>

</form>