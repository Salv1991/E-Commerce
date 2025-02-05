<x-layout :hideHeader="true" :hideFooter="true">
   <x-admin.layout title="Orders">
        <div class="overflow-x-auto w-full pb-4">
            <table class="table-auto w-full mt-5 min-w-[800px]">
                <thead class="">
                    <tr class="*:text-end bg-gray-200 *:px-3 *:whitespace-nowrap">
                        <th>Id</th>
                        <th>User id</th>
                        <th>Status</th>
                        <th>Total Price</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Payment Method</th>
                        <th>Payment Fee</th>
                        <th>Shipping Method</th>
                        <th>Shipping Fee</th>
                        <th>Paid</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2">
                    @forelse($orders as $order)
                        <tr class="*:text-end hover:bg-gray-50 ">
                            <td><a href="{{route('admin.order.edit.show', $order->id)}}" class="block w-full h-full px-3">{{$order->id}}</a></td>
                            <td><a href="{{route('admin.order.edit.show', $order->id)}}" class="block w-full h-full px-3">{{$order->user_id ?? '-'}}</a></td>
                            <td>
                                <a 
                                    href="{{route('admin.order.edit.show', $order->id)}}" 
                                    @class([
                                        'text-green-500' => $order->status == 'completed', 
                                        'text-orange-400' => $order->status == 'pending', 
                                        'text-red-500' => $order->status == 'cancelled', 
                                        'block w-full h-full px-3'
                                    ])>
                                    {{$order->status}}
                                </a>
                            </td>
                            <td><a href="{{route('admin.order.edit.show', $order->id)}}" class="block w-full h-full px-3">{{$order->total_price}}$</a></td>
                            <td><a href="{{route('admin.order.edit.show', $order->id)}}" class="block w-full h-full px-3">{{$order->subtotal}}$</a></td>
                            <td><a href="{{route('admin.order.edit.show', $order->id)}}" class="block w-full h-full px-3">{{$order->discount ?? '-'}}</a></td>
                            <td><a href="{{route('admin.order.edit.show', $order->id)}}" class="block w-full h-full px-3">{{$order->payment_method}}</a></td>
                            <td><a href="{{route('admin.order.edit.show', $order->id)}}" class="block w-full h-full px-3">{{$order->payment_fee}}$</a></td>
                            <td><a href="{{route('admin.order.edit.show', $order->id)}}" class="block w-full h-full px-3">{{$order->shipping_method}}</a></td>
                            <td><a href="{{route('admin.order.edit.show', $order->id)}}" class="block w-full h-full px-3">{{$order->shipping_fee}}$</a></td>
                            <td><a href="{{route('admin.order.edit.show', $order->id)}}" class="block w-full h-full px-3 whitespace-nowrap {{ $order->paid == 'true' ? 'text-green-400' : 'text-red-500' }}">{{$order->paid ? 'Paid' : 'Not paid'}}</a></td>
                        </tr>
                    @empty
                    <tr class="">
                        <td colspan="7" class="text-center p-2">No categories found</td>
                    </tr> 
                    @endforelse
                </tbody>
            </table> 
        </div>    

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $orders->links() }}
        </div> 

   </x-admin.layout>
</x-layout>