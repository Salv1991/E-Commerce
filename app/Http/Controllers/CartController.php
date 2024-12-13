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

        $cartData = $this->cart->getCartData();
        $cartTotal = $cartData['cartTotal'];
        $cart = $cartData['cart'];
        return view('cart.index', compact(['cart', 'cartTotal','wishlistedProductsIds']));
    }

    public function add($id){
        $product = Product::find($id);
        $cartTotal = 0;
        $cartCount = 0;
        $lineItemExists = false;

        if(!$product){
            return response()->json(['error' => 'Product not found.']);
        }

        if(Auth::check()){
            $user = Auth::user();
            $currentOrder = $user->currentOrder()->with('lineItems')->first();
            
            if(!$currentOrder){
                $currentOrder = Order::create([
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'total_price' => 0,
                ]);
            }
            
            $lineItem = $currentOrder->lineItems->firstWhere('product_id', $product->id);

            if($lineItem) {
                if($lineItem->quantity >= $product->stock){
                    return response()->json(['error' => 'Not enough stock available']);
                }; 

                $lineItem->increment('quantity');
                $lineItemExists = true;
            } else {
                $lineItem = LineItem::create([
                    'order_id' => $currentOrder->id,
                    'product_id' => $id,
                    'quantity' => 1,
                    'price' => $product->current_price 
                ]);

            }

            $currentOrder = $currentOrder->refresh();
            $cartCount = $currentOrder->lineItems->sum('quantity'); 
            $cartTotal = $currentOrder->total_price; 
        } else {
            $cart = session()->get('cart', []);

            if(isset($cart[$id])){
                if($cart[$id]['quantity'] >= $product->stock){
                    return response()->json(['error' => 'Not enough stock available']);
                } 

                $cart[$id]['quantity']++;
                $lineItemExists = true;
            }else{
                $cart[$id] = [
                    'product_id' => $id,
                    'quantity' => 1,
                    'price' => $product->current_price,    
                ];
            }

            $cartCount = array_sum(array_column($cart, 'quantity'));
            $cartTotal = array_reduce($cart, function($total, $product){
                return $total + ($product['quantity'] * $product['price']);
            }, 0);

            session()->put('cart', $cart);
        }

        if(request()->ajax()){
            $quantity = $lineItem->quantity ?? $cart[$id]['quantity'];

            $data = [
                'product_id' => $product->id,
                'cartTotal' => number_format($cartTotal, 2),
                'cartCount' => $cartCount,
                'lineItemExists' => $lineItemExists
            ];

            if(!$lineItemExists){
                $data['view'] = view('components.nav.cart-teaser', compact(['product', 'quantity']))->render();           
            } else {
                $data['title'] = $product->title;
                $data['quantity'] = $quantity;
                $data['total'] = number_format($product->current_price * $quantity, 2);
            }

            return response()->json($data);
        }

        return redirect()->back();
    }

    public function quantity(Request $request, $id){
        $request->validate([
            'quantity' => 'integer|min:0|required'
        ]);

        if(Auth::check()){   
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

            if($product && isset($cart[$id])){
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
        $cartTotal = 0;
        $cartCount = 0;

        if(Auth::check()){     
            $currentOrder = Auth::user()->currentOrder()->with('lineItems')->first();

            if($currentOrder){
                $lineItem = $currentOrder->lineItems->firstWhere('product_id', $id);

                if($lineItem){
                    $lineItem->delete();
                }

                $currentOrder = $currentOrder->refresh();
                $cartCount = $currentOrder->lineItems->sum('quantity'); 
                $cartTotal = $currentOrder->total_price;             }
        } else {
            $cart = session()->get('cart', []);

            if(isset($cart[$id])){
                unset($cart[$id]);
                session()->put('cart', $cart);

                $cartCount = array_sum(array_column($cart, 'quantity'));
                $cartTotal = array_reduce($cart, function($total, $product){
                    return $total + ($product['quantity'] * $product['price']);
                }, 0);

            }
        }

        if(request()->ajax()){
            return response()->json([
                'product_id' => $id,
                'cartTotal' => number_format($cartTotal, 2),
                'cartCount' => $cartCount,
            ]);
        }

        return redirect()->back();
    }

}
