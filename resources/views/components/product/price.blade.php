<div class="w-fit *:inline-block">
    <p @class([
            'text-base font-semibold text-gray-400',
            'line-through' => $product->discounted_price,
        ])>
        ${{$product->price}}
    </p>

    @if ($product->discounted_price)
        <p class="text-base font-bold text-primary-500">${{$product->discounted_price}}</p>   
    @endif
</div>