<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\LineItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(){
        $cart = collect();
        $cartTotal = 0;
        $wishlistedProductsIds = collect();

        if(auth()->check()) {
            $currentOrder = auth()->user()->currentOrder()->with('lineItems.product')->first();
            $cartTotal = $currentOrder->total_price;
            $cart = $currentOrder?->lineItems ?? $cart;
            $wishlistedProductsIds = auth()->user()->wishlistedProductsIds();
        };

        return view('cart.index', compact(['cart', 'cartTotal', 'wishlistedProductsIds']));
    }

    public function add($id){
        $user = auth()->user();
        $currentOrder = $user->currentOrder()->first();
        
        if(!$currentOrder){
            $currentOrder = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_price' => 0,
            ]);
        }
        
        $product = Product::find($id);

        if(!$product){
            return redirect()->back()->withErrors(['error' => 'Product not found.']);
        }

        $lineItem = LineItem::where('order_id', $currentOrder->id)
            ->where('product_id', $product->id)
            ->first();

        if($lineItem) {
            $lineItem->increment('quantity');
            //$currentOrder->increment('total_price', $product->current_price);
        }else{
            LineItem::create([
                'order_id' => $currentOrder->id,
                'product_id' => $id,
                'quantity' => 1,
                'price' => $product->current_price 
            ]);
            //$currentOrder->increment('total_price', $product->current_price);
        }

        return redirect()->back();
    }

    public function quantity(Request $request, $id){
        $request->validate([
            'quantity' => 'integer|min:0|required'
        ]);

        $lineItem = LineItem::where('id', $id)
            ->where('order_id', auth()->user()->currentOrder->id)
            ->firstOrFail();

        if($request->quantity > $lineItem->product->stock){
            return redirect()->route('cart.index')->with('error', 'Not enough stock available');
        }

        if($request->quantity > 0){
            $lineItem->update([
                'quantity' => $request->quantity    
            ]); 
        }else if($request->quantity == 0) {
            $lineItem->delete();
        }

        return redirect()->back()->with('success', 'Cart updated successfully');
    }

    public function delete($id){
        $user = auth()->user();
        $currentOrder = $user->currentOrder()->first();
        if($currentOrder){

            $lineItem = LineItem::where('order_id', $currentOrder->id)
                ->where('product_id', $id)
                ->first();
            if($lineItem){
                $lineItem->delete();
            }
        }
        return redirect()->back();
    }

}
