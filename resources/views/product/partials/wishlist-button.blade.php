@props(['id' , 'isAdded' => false])

<form class="wishlist-form" data-product-id="{{ $id }}" method="POST" action="{{ $isAdded ? route('wishlist.destroy', $id) : route('wishlist.create', $id) }}">
    @csrf
    @if($isAdded)
        @method('DELETE')
        <button type="submit" class="mx-auto block text-xl">
            <x-heroicon-o-heart class="inline-block w-8 h-8 fill-red-300 text-red-300 -translate-y-1" />
        </button>
    @else
        <button type="submit" class="mx-auto block">
            <x-heroicon-o-heart class="inline-block w-8 h-8 hover:fill-red-300 hover:text-red-300 -translate-y-1" />
        </button>
    @endif
</form>
