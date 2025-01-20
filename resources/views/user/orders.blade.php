<x-layout>
    <div class="max-w-screen-xl m-auto p-10">
        <h1 class="text-center text-4xl font-semibold">My orders</h1>
        <div class="overflow-x-auto">
            <table class="table-auto w-full mt-20 min-w-[800px]">
                <thead class="">
                    <tr class="*:text-end bg-gray-200">
                        <th>Status</th>
                        <th>Total price</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Payment method</th>
                        <th>Shipping method</th>
                        <th>Paid</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2">
                    @foreach($orders as $order)
                    <tr class="*:text-end">
                        <td>{{$order->status}}</td>
                        <td>{{$order->total_price}}$</td>
                        <td>{{$order->subtotal}}$</td>
                        <td>{{$order->discount ?? '-'}}</td>
                        <td>{{config('app.payment_methods')[$order->payment_method]['title']}}</td>
                        <td>{{config('app.shipping_methods')[$order->shipping_method]['title']}}</td>
                        <td>{{$order->paid == 0 ? 'Not paid' : 'Paid'}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table> 
        </div>         
    </div>
</x-layout>