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

        if(Auth::check()) {
            $wishlistedProductsIds = Auth::user()->wishlistedProductsIds();
        };

        $cartData = $this->cart->getCartData();
        $cartTotal = $cartData['cartTotal'];
        $cart = $cartData['cart'];
        return view('cart', compact(['cart', 'cartTotal', 'wishlistedProductsIds']));
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
                 if($product->stock <= 0){
                    return response()->json(['error' => 'Not enough stock available']);
                }; 
                
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
                'message' => 'Product added to cart',
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

    //replace lineItem id with product id? 
    public function quantity(Request $request, $id){
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        $cartTotal = 0;
        $cartCount = 0;
        $quantity = 0;

        if(Auth::check()){   
            $currentOrder = Auth::user()->currentOrder()->with('lineItems')->first();

            $lineItem = LineItem::where('product_id', $id)
                ->where('order_id', $currentOrder->id)
                ->firstOrFail();

            $quantity = min($request->quantity, $lineItem->product->stock);
          
            if($request->quantity <= 0) {
                $lineItem->delete();
            } else {          
                $lineItem->update(['quantity' => $quantity]); 
            }

            $currentOrder = $currentOrder->refresh();
            $cartCount = $currentOrder->lineItems->sum('quantity'); 
            $cartTotal = $currentOrder->total_price; 

        } else {
            $cart = session()->get('cart', []);
            $product = Product::find($id);

            if($product && isset($cart[$id])){
                $quantity = min($request->quantity, $product->stock);

                if($quantity <= 0){
                    unset($cart[$id]);
                } else {
                    $cart[$id]['quantity'] = $quantity;
                }
                      
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
                'quantity' => $quantity,
                'cartTotal' => number_format($cartTotal, 2),
                'cartCount' => $cartCount  
            ]);
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
                'message' => 'Product removed from cart.',
                'product_id' => $id,
                'cartTotal' => number_format($cartTotal, 2),
                'cartCount' => $cartCount,
            ]);
        }

        return redirect()->back();
    }
   
}
