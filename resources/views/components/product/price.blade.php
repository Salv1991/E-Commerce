<div {{ $attributes->merge(['class' => 'w-fit *:inline-block flex justify-start items-center gap-2']) }}>
    @if ($product->discount)   
        <div class="w-fit font-semibold px-2 py-[1px] rounded-[3px] text-[11px] bg-black">
            <p class="text-white">-{{ $product->discount }}%</p>
        </div> 
        <p class="text-base font-bold text-primary-500">${{ number_format($product->current_price, 2) }}</p>   
    @endif
    <p @class([
            'text-base font-semibold',
            'text-gray-600' => !$product->discount,
            'line-through text-gray-400' => $product->discount,
        ])>
        ${{ number_format($product->original_price, 2) }}
    </p> 
    
</div>