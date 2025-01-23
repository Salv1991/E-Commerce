<form 
    action="{{ route('wishlist.toggle' , $product->id) }}" 
    method="post" 
    class="wishlist-form" 
    data-action="submit->wishlist#toggle"
    data-product-id="{{ $product->id }}"> 
    @csrf

    <button type="submit" class="mx-auto block text-xl">
        <x-heroicon-o-heart @class([
            'inline-block w-8 h-8 wishlist-icon transform transition-all duration-300',
            'fill-red-400 text-white/10' => $isWishlisted,
            'fill-gray-300/70 text-transparent' => !$isWishlisted
            ])/>
    </button>
</form>
