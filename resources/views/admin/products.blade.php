<x-layout :hideHeader="true" :hideFooter="true">
   <x-admin.layout title="Products">
        <a href="{{route('admin.product.create')}}" class="ml-auto bg-black hover:bg-black/80  text-white w-fit px-4 py-2 flex justify-between items-center gap-2">
            <x-heroicon-m-plus class="w-5 h-5"/>
            <span>Create Product</span>
        </a>
        <div class="overflow-x-auto pb-4">
            <table class="table-auto w-full mt-5 min-w-[800px]">
                <thead class="">
                    <tr class="*:text-end bg-gray-300">
                        <th>Id</th>
                        <th>mpn</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Current price</th>
                        <th>Original price</th>
                        <th>Discount</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2">
                    @forelse($products as $product)
                        <tr class="*:text-end hover:bg-gray-200">
                            <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->id}}</a></td>
                            <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->mpn ?? '-'}}</a></td>
                            <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->title}}</a></td>
                            <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->discount ?? '-'}}</a></td>
                            <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->current_price}}$</a></td>
                            <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->original_price}}$</a></td>
                            <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->discount ?? '-'}}</a></td>
                            <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full px-1">{{$product->stock ?? '-'}}</a></td>
                        </tr>
                    @empty
                    <tr class="">
                        <td colspan="7" class="text-center p-2">No products found</td>
                    </tr> 
                    @endforelse
                </tbody>
            </table> 
        </div>   

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $products->links() }}
        </div> 

   </x-admin.layout>
</x-layout>