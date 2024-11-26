<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(){

        $cart = auth()->check() ? auth()->user()->cart()->with('product')->get() : [];

        $wishlistedProductsIds = auth()->check()
            ? auth()->user()->wishlistedProductsIds()
            : [];
            
        return view('cart.index', compact(['cart', 'wishlistedProductsIds']));
    }

    public function add($id){
        
        $lineItem = Cart::where('user_id', auth()->user()->id)
            ->where('product_id', $id)
            ->first();

        if($lineItem) {
            $lineItem->increment('quantity');
        }else{
            Cart::create([
                'user_id' => auth()->user()->id,
                'product_id' => $id   
            ]);
        }
        
        return redirect()->back();
    }

    public function delete($id){
        $lineItem = Cart::findOrFail($id);

        $lineItem->delete();
        
        return redirect()->route('cart.index');
    }

}
