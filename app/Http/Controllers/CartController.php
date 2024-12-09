<?php

namespace App\Http\Controllers;

use App\Models\LineItem;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(protected CartService $cart){}

    public function index(){
        $wishlistedProductsIds = collect();
        $user = Auth::user();

        if(Auth::check()) {
            $wishlistedProductsIds = $user->wishlistedProductsIds();
        };

        //$cartData = $this->cart->getCartData();
        //$cartTotal = $cartData['cartTotal'];
        //$cart = $cartData['cart'];
        
        return view('cart.index', compact(['wishlistedProductsIds']));
    }

    public function add($id){
        $product = Product::find($id);

        if(!$product){
            return redirect()->back()->with('error', 'Product not found.');
        }

        if(Auth::check()){
            $user = Auth::user();
            $currentOrder = $user->currentOrder()->first();
            
            if(!$currentOrder){
                $currentOrder = Order::create([
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'total_price' => 0,
                ]);
            }
            
            $lineItem = LineItem::where('order_id', $currentOrder->id)
                ->where('product_id', $product->id)
                ->first();

            if($lineItem) {
                if($lineItem->quantity >= $product->stock){
                    return redirect()->back()->with('error', 'Not enough stock available');
                }; 

                $lineItem->increment('quantity');
            } else {
                LineItem::create([
                    'order_id' => $currentOrder->id,
                    'product_id' => $id,
                    'quantity' => 1,
                    'price' => $product->current_price 
                ]);
            }

        } else {
            $cart = session()->get('cart', []);

            if(isset($cart[$id])){
                if($cart[$id]['quantity'] >= $product->stock){
                    return redirect()->back()->with('error', 'Not enough stock available');
                } 

                $cart[$id]['quantity']++;
            }else{
                $cart[$id] = [
                    'product_id' => $id,
                    'quantity' => 1,
                    'price' => $product->current_price,    
                ];
            }
            
            session()->put('cart', $cart);
        }

        return redirect()->back();
    }

    public function quantity(Request $request, $id){
        $request->validate([
            'quantity' => 'integer|min:0|required'
        ]);

        if( Auth::check()){   
            $lineItem = LineItem::where('id', $id)
                ->where('order_id', Auth::user()->currentOrder->id)
                ->firstOrFail();
          
            if($request->quantity <= 0) {
                $lineItem->delete();
            } else {          
                $lineItem->update([
                    'quantity' => min($request->quantity, $lineItem->product->stock)    
                ]); 
            }

        } else {
            $cart = session()->get('cart', []);
            $product = Product::find($id);

            if(isset($cart[$id]) && $product){
                if($request->quantity > $product->stock){
                    return redirect()->route('cart.index')->with('error', 'Not enough stock available');
                }
                
                if($request->quantity > 0){
                    $cart[$id]['quantity'] = $request->quantity;
                }
                
                if($request->quantity == 0){
                    unset($cart[$id]);
                }
            }

            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Cart updated successfully');
    }

    public function delete($id){
        if(Auth::check()){     
            $currentOrder = Auth::user()->currentOrder()->first();

            if($currentOrder){
                $lineItem = LineItem::where('order_id', $currentOrder->id)
                    ->where('product_id', $id)
                    ->first();
                if($lineItem){
                    $lineItem->delete();
                }
            }
        } else {
            $cart = session()->get('cart', []);

            if(isset($cart[$id])){
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
        }

        return redirect()->back();
    }

}
