<turbo-stream action="replace" target="wishlist-frame-{{ $product->id }}">
    <template>
        <div class="absolute top-4 right-4 w-fit m-auto">
            @if ($product->is_wishlisted)
                <form action="{{ route('wishlist.destroy', $product->id) }}" method="post" class="wishlist-form" data-turbo="true">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="mx-auto block text-xl">
                        <x-heroicon-o-heart class="inline-block w-8 h-8 fill-red-300 text-red-300 -translate-y-1" />
                    </button>
                </form>
            @else
                <form action="{{ route('wishlist.create', $product->id) }}" method="post" class="wishlist-form" data-turbo="true">
                    @csrf
                    <button type="submit" class="mx-auto block">
                        <x-heroicon-o-heart class="inline-block w-8 h-8 hover:fill-red-300 hover:text-red-300 -translate-y-1" />
                    </button>
                </form>
            @endif
        </div>
    </template>
</turbo-stream>