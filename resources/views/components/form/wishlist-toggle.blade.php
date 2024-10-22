<form 
    action="{{ route('wishlist.create' , $product->id) }}" 
    method="post" 
    class="wishlist-form" 
    data-action="submit->wishlist#submit" 
    data-wishlist-target="form"
    data-product-id="{{ $product->id }}"> 

    @csrf

    <button type="submit" class="mx-auto block text-xl">
        <x-heroicon-o-heart @class([
            'inline-block w-8 h-8 -translate-y-1',
            'fill-red-300 text-red-300' => $isWishlisted ,
            'hover:fill-red-300 hover:text-red-300' => !$isWishlisted ,
            ])/>
    </button>
</form>
