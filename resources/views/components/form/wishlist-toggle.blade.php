<form 
    action="{{ route('wishlist.toggle' , $product->id) }}" 
    method="post" 
    class="wishlist-form" 
    data-wishlist-target="form"
    data-action="submit->wishlist#toggle"
    data-product-id="{{ $product->id }}"> 

    @csrf

    <button type="submit" class="mx-auto block text-xl">
        <x-heroicon-o-heart @class([
            'inline-block w-8 h-8',
            'fill-red-300 text-red-300' => $isWishlisted 
            ])/>
    </button>
</form>
