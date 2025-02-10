<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showList() {
        $orders = Order::paginate(20);
        return view('admin.orders', compact(['orders']));
    }

    public function order($id) {
        $order = Order::find($id);

        if(!$order){
            return redirect()->back()->with('error', "Order doesn't exist");
        }
        
        return view('admin.order.edit', compact(['order']));
    }

    public function editOrder(Request $request, $id) {

        $order = Order::find($id);

        if(!$order){
            return redirect()->back()->with('error', "Order doesn't exist");
        }

        $availableShippingMethods = config('app.shipping_methods');
        $availablePaymentMethods = config('app.payment_methods');
        
        $validatedData = $request->validate([
            'status' => ['required', 'in:pending,completed,cancelled'],
            'payment_method' => ['required', 'string', 'in:' . implode( ',', array_keys( $availablePaymentMethods ))],
            'shipping_method' => ['required', 'string', 'in:' . implode( ',', array_keys( $availableShippingMethods ))],
            'paid' => ['required', 'boolean'],
        ]);

        if($order->subtotal == 0 || $order->subtotal >= config('app.free_shipping_min_subtotal')) {
            $shipping_fee = 0;    
        } else {
            $shipping_fee = $availableShippingMethods[$validatedData['shipping_method']]['extra_cost'];
        }

        $payment_fee = $availablePaymentMethods[$validatedData['payment_method']]['extra_cost'];
        
        $order->update([
            'total_price' => $order->subtotal + $payment_fee + $shipping_fee,
            'status' => $validatedData['status'],
            'payment_method' => $validatedData['payment_method'],
            'payment_fee' =>  $payment_fee ,
            'shipping_method' => $validatedData['shipping_method'],
            'shipping_fee' => $shipping_fee,
            'paid' => $validatedData['paid'],
        ]);

        return redirect()->back()->with('success', 'Order updated successfully');
    }


}
