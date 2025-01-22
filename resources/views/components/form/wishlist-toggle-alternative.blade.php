<form 
    method="post"
    action="{{ route('wishlist.toggle', $product->id) }}" 
    class="m-auto bg-white text-black border-2 border-black group"
    data-action="submit->wishlist#toggle"
    data-product-id="{{ $product->id }}">
    @csrf

    <button type="submit" class="px-4 py-4 w-full text-xl flex justify-center items-center gap-2">
        <span class="wishlist-text">{{$isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'}}</span>
        <x-heroicon-o-heart @class([
            'inline-block w-8 h-8 -translate-y-1 wishlist-icon transform transition-transform duration-500',
            'fill-red-400 text-white/10' => $isWishlisted,
            'fill-gray-300/70 text-transparent' => !$isWishlisted
            ])/>    
    </button>
</form>