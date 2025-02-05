<x-layout :hideHeader="true" :hideFooter="true">
   <x-admin.layout title="{{$order->title}} (id:{{$order->id}})">
    <form class="mt-10" method="post" action="{{route('admin.order.edit.store', $order->id)}}">
            @method('PATCH')
            @csrf
            
            <x-form.input readonly label="Total Price" name="total_price" value="{{ old('total_price', $order->total_price) }}" placeholder="Total Price"/>
            <x-form.input readonly label="Subtotal" name="subtotal" value="{{ old('subtotal', $order->subtotal) }}" placeholder="Subtotal"/>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label for="payment_method" class="block text-xs font-bold mt-5">
                    Payment Method *
                    <select name="payment_method" id="payment_method" class="w-full mt-2 p-2">
                        @foreach (config('app.payment_methods') as $key => $payment_method)
                            <option value="{{ $key }}" class="text-black" {{ $order->payment_method == $key ? 'selected' : '' }}>
                                {{ $payment_method['title'] }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label for="shipping_method" class="block text-xs font-bold mt-5">
                    Shipping Method *
                    <select name="shipping_method" id="shipping_method" class="w-full mt-2 p-2">
                        @foreach (config('app.shipping_methods') as $key => $shipping_method)
                            <option value="{{ $key }}" class="text-black" {{ $order->shipping_method == $key ? 'selected' : '' }}>
                                {{ $shipping_method['title'] }}
                            </option>
                        @endforeach
                    </select>
                </label>
                
                <label for="paid" class="block text-xs font-bold mt-5">
                    Paid *
                    <select name="paid" id="paid" class="w-full mt-2 p-2">
                        <option value="true" class="text-black" {{ $order->paid  ? 'selected' : '' }}>Paid</option>
                        <option value="false" class="text-black" {{ $order->paid ? '' : 'selected' }}>Not paid</option>
                    </select>
                </label> 
            </div>
                     
            <div class="ml-auto w-full sm:w-fit">
                <button type="submit" class="bg-black mt-10 w-full rounded-sm hover:underline underline-offset-4 px-20 py-4 text-white">Save changes</button>
            </div>
        </form>
   </x-admin.layout>
</x-layout>