<x-layout :hideHeader="true" :hideFooter="true">
   <x-admin.layout title="Products">
      <div class="overflow-x-auto">
            <table class="table-auto w-full mt-5 min-w-[800px]">
                <thead class="">
                    <tr class="*:text-end bg-gray-200">
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
                    <a href="{{route('cart')}}" class="hover:bg-primary-500">
                     <tr class="*:text-end hover:bg-primary-500 hover:text-white">
                           <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->id}}</a></td>
                           <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->mpn ?? '-'}}</a></td>
                           <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->title}}</a></td>
                           <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->discount ?? '-'}}</a></td>
                           <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->current_price}}$</a></td>
                           <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->original_price}}$</a></td>
                           <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full">{{$product->discount ?? '-'}}</a></td>
                           <td><a href="{{route('admin.product.edit', $product->id)}}" class="block w-full px-1">{{$product->stock ?? '-'}}</a></td>
                     </tr>
                    </a>
                    @empty
                    <tr class="">
                        <td colspan="7" class="text-center p-2">No products found</td>

                    </tr> 
                    @endforelse
                </tbody>
            </table> 
        </div>    
   </x-admin.layout>
</x-layout>