<form 
    id="wishlist-form-show"
    action="{{ route('wishlist.toggle', $product->id) }}" 
    data-product-id="{{ $product->id }}" 
    method="post"
    data-view-type="show"
    data-action="submit->wishlist#toggle"
    class="m-auto bg-white text-black border-2 border-black group">
    @csrf

    <button  type="submit" class="px-4 py-4 w-full text-xl flex justify-center items-center gap-2">
        <span>{{$isWishlisted ? 'Remove from Wishlist' : 'Add from Wishlist'}}</span>
        <x-heroicon-o-heart @class([
            'inline-block w-8 h-8 -translate-y-1',
            'fill-red-300 text-red-300' => $isWishlisted 
            ])/>    
    </button>
</form>