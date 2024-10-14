<x-layout>
<div class="w-full p-5 max-w-screen-xl m-auto">

    <div class="grid grid-cols-4 gap-3">
    @foreach ($products as $product)
        <x-product.teaser :product="$product" />
    @endforeach
    </div>
</div>

</x-layout>