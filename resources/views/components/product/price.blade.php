<div class="w-fit *:inline-block">
    <p @class([
            'text-base font-semibold text-gray-400',
            'line-through' => $product->discount,
        ])>
        ${{ number_format($product->original_price, 2) }}
    </p>

    @if ($product->discount)
        <p class="text-base font-bold text-primary-500">${{ number_format($product->current_price, 2) }}</p>   
    @endif
</div>