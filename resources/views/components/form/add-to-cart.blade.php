<form action="{{ route('cart.add', $product->id) }}" method="post">
    @csrf
    <button type="submit" 
        class="w-full p-2 rounded-sm hover:bg-black text-black hover:text-white duration-300">
        <x-heroicon-c-shopping-bag class="inline-block w-6 h-6"/>
    </button>
</form>