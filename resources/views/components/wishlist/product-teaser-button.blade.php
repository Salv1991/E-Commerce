@props(['productId' => null, 'isAdded' => false])

@if ($productId)
<div class="wishlist-container absolute top-4 right-4 w-fit m-auto z-50">
   
    <form class="wishlist-form" data-product-id="{{ $productId }}" method="POST" action="{{ $isAdded ? route('wishlist.toggle', $productId) : route('wishlist.toggle', $productId) }}">
        @csrf
        @if($isAdded)
            <button type="submit" class="mx-auto block text-xl">
                <x-heroicon-o-heart class="inline-block w-8 h-8 fill-red-300 text-red-300 -translate-y-1" />
            </button>
        @else
            <button type="submit" class="mx-auto block">
                <x-heroicon-o-heart class="inline-block w-8 h-8 hover:fill-red-300 hover:text-red-300 -translate-y-1" />
            </button>
        @endif
    </form>
    @endif
</div>