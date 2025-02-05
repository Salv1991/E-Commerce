<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function showList() {
        $orders = Order::paginate(20);
        return view('admin.orders', compact(['orders']));
    }

    public function order($id) {
        $order = Order::findOrFail($id);

        return view('admin.order.edit', compact(['order']));
    }
}
