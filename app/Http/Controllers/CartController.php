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
        $wishlistedProductsIds = collect();

        if(auth()->check()) {
            $currentOrder = auth()->user()->currentOrder()->with('lineItems.product')->first();
            $cart = $currentOrder?->lineItems ?? $cart;
            $wishlistedProductsIds = auth()->user()->wishlistedProductsIds();
        };

        return view('cart.index', compact(['cart', 'wishlistedProductsIds']));
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
