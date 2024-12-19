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
</form>