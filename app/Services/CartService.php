<?php

namespace App\Services;

use App\Models\LineItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCartData()
    {
        $cartData = [
            'cart' => collect(),
            'cartCount' => 0,
            'cartSubtotal' => 0,
            'shippingFee' => 0
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $currentOrder = $user->currentOrder()->with('lineItems.product.images')->first();

            if ($currentOrder) {
                $cartData['cart'] = $currentOrder->lineItems->map(function ($lineItem) {
                    return (object)[
                        'id' => $lineItem->id,
                        'product' => $lineItem->product,
                        'quantity' => $lineItem->quantity,
                        'price' => $lineItem->quantity * $lineItem->product->current_price,    
                    ];
                });
                $cartData['cartCount'] = $currentOrder->lineItemsQuantity();
                $cartData['cartSubtotal'] = $currentOrder->subtotal;
                $cartData['shippingFee'] = $currentOrder->shipping_fee;
            }

        } else {
            $guestCart = collect(Session::get('cart', []));

            if ($guestCart->isNotEmpty()) {
                $productIds = $guestCart->pluck('product_id')->unique();
                $products = Product::with('images')->whereIn('id', $productIds)->get();

                $cartData['cart'] = $products->map(function ($product) use ($guestCart) {
                    if (isset($guestCart[$product->id])) {
                        $quantity = $guestCart[$product->id]['quantity'];
                        return (object)[
                            'id' => $product->id,
                            'product' => $product,
                            'quantity' => $quantity,
                            'price' => $quantity * $product->current_price,    
                        ];
                    }
                })->filter();

                $cartData['cartCount'] = $cartData['cart']->sum('quantity');
                $cartData['cartSubtotal'] = $cartData['cart']->sum(function ($product) {
                    return $product->quantity * $product->price;
                });

                if($cartData['cartSubtotal'] == 0 || $cartData['cartSubtotal'] > config('app.free_shipping_min_subtotal')) {
                    $cartData['shippingFee'] = 0;
                } else {
                    $cartData['shippingFee'] = number_format(config('app.shipping_fee'), 2);
                }
            }
        }

        return $cartData;
    }

    public function addProductToCart($id){
        $product = Product::find($id);
        $cartSubtotal = 0;
        $cartCount = 0;
        $lineItemExists = false;

        if(!$product){
            return ['error' => 'Product not found.'];
        }

        if(Auth::check()){
            $user = Auth::user();
            $currentOrder = $user->currentOrder()->with('lineItems')->first();
            
            if(!$currentOrder){
                $currentOrder = Order::create([
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'subtotal' => 0,
                    'total_price' => 0,
                ]);
            }
            
            $lineItem = $currentOrder->lineItems->firstWhere('product_id', $product->id);

            if($lineItem) {
                if($lineItem->quantity >= $product->stock){
                    return ['error' => 'Not enough stock available'];
                }; 

                $lineItem->increment('quantity');
                $lineItemExists = true;
            } else {
                 if($product->stock <= 0){
                    return ['error' => 'Not enough stock available'];
                }; 
                
                $lineItem = LineItem::create([
                    'order_id' => $currentOrder->id,
                    'product_id' => $id,
                    'quantity' => 1,
                    'price' => $product->current_price 
                ]);

            }

            $currentOrder = $currentOrder->refresh();
            $orderSummary = $this->calculateOrderSummaryForUser($currentOrder);         
        } else {
            $cart = session()->get('cart', []);

            if(isset($cart[$id])){
                if($cart[$id]['quantity'] >= $product->stock){
                    return ['error' => 'Not enough stock available'];
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

            session()->put('cart', $cart);
            $orderSummary = $this->calculateOrderSummaryForGuest($cart);         
        }

        $quantity = $lineItem->quantity ?? $cart[$id]['quantity'];

        $data = [
            'message' => 'Product added to cart',
            'product_id' => $product->id,
            'lineItemExists' => $lineItemExists,
            'cartCount' => $orderSummary['cartCount'],
            'vatPrice' => $orderSummary['vatPrice'],
            'cartSubtotal' => $orderSummary['cartSubtotal'],
            'cartTotal' => $orderSummary['cartTotal'],
        ];

        if(!$lineItemExists){
            $data['view'] = view('components.nav.cart-teaser', compact(['product', 'quantity']))->render();           
        } else {
            $data['title'] = $product->title;
            $data['quantity'] = $quantity;
            $data['total'] = number_format($product->current_price * $quantity, 2);
        }

        return $data;
    }

    public function getCartCount() {
        $cartCount = 0;

        if (Auth::check()) {
            $user = Auth::user();
            $currentOrder = $user->currentOrder()->first();

            if ($currentOrder) {
                $cartCount = $currentOrder->lineItemsQuantity();
            }
        } else {
            $guestCart = collect(Session::get('cart', []));

            if ($guestCart->isNotEmpty()) {
                $cartCount = $guestCart->sum('quantity');
            }
        }

        return $cartCount;
    }

    public function updateQuantity($requestQuantity, $id) {      
        $quantity = 0;

        if(Auth::check()){   
            $currentOrder = Auth::user()->currentOrder()->with('lineItems.product')->first();

            $lineItem = $currentOrder->lineItems->firstWhere('product_id', $id);

            $quantity = min($requestQuantity, $lineItem->product->stock);
          
            if($requestQuantity <= 0) {
                $lineItem->delete();
            } else {          
                $lineItem->update(['quantity' => $quantity]); 
            }

            $currentOrder = $currentOrder->refresh();

            $orderSummary = $this->calculateOrderSummaryForUser($currentOrder);         
        } else {
            $cart = session()->get('cart', []);
            $product = Product::find($id);

            if(!$product || !isset($cart[$id])){
                return ['error' => 'Product not found.'];
            }

            $quantity = min($requestQuantity, $product->stock);

            if($quantity <= 0){
                unset($cart[$id]);
            } else {
                $cart[$id]['quantity'] = $quantity;
            }
                    
            session()->put('cart', $cart);
            
            $orderSummary = $this->calculateOrderSummaryForGuest($cart);
         
        }

        return [
            'product_id' => $id,
            'quantity' => $quantity,
            'cartCount' => $orderSummary['cartCount'],
            'vatPrice' => $orderSummary['vatPrice'],
            'shippingFee' => $orderSummary['shippingFee'],
            'cartSubtotal' => $orderSummary['cartSubtotal'],
            'cartTotal' => $orderSummary['cartTotal'],
        ];
    }


    public function deleteProductFromCart($id){
        if(Auth::check()){     
            $currentOrder = Auth::user()->currentOrder()->with('lineItems')->first();

            if($currentOrder){
                $lineItem = $currentOrder->lineItems->firstWhere('product_id', $id);

                if($lineItem){
                    $lineItem->delete();
                }

                $currentOrder = $currentOrder->refresh();
                $orderSummary = $this->calculateOrderSummaryForUser($currentOrder);         
            } else {
                return ['error' => 'Product not found.'];
            }
        } else {
            $cart = session()->get('cart', []);

            if(!isset($cart[$id])){
                return ['error' => 'Product not found.'];          
            }
        
            unset($cart[$id]);         
            session()->put('cart', $cart);

            $orderSummary = $this->calculateOrderSummaryForGuest($cart);           
        }

        return [
            'message' => 'Product removed from cart.',
            'product_id' => $id,
            'cartCount' => $orderSummary['cartCount'],
            'vatPrice' => $orderSummary['vatPrice'],
            'shippingFee' => $orderSummary['shippingFee'],
            'cartSubtotal' => $orderSummary['cartSubtotal'],
            'cartTotal' => $orderSummary['cartTotal'],
        ];
    }

    protected function calculateOrderSummaryForUser($order) {
        $cartCount = $order->lineItems->sum('quantity'); 
        $cartSubtotal = $order->subtotal; 
        $shippingFee = $order->shipping_fee;
        $vatPrice = $cartSubtotal * config('app.vat_rate');

        return [
            'cartCount' => $cartCount,
            'cartSubtotal' => number_format($cartSubtotal, 2),
            'vatPrice' => number_format($vatPrice, 2),
            'shippingFee' =>$shippingFee,
            'cartTotal' => number_format($cartSubtotal + $shippingFee, 2),
        ];
    }  

    protected function calculateOrderSummaryForGuest($cart) {
        $shippingFee = 0;
        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartSubtotal = array_reduce($cart, function($total, $product){
            return $total + ($product['quantity'] * $product['price']);
        }, 0);

        if ($cartSubtotal > 0 && $cartSubtotal <= config('app.free_shipping_min_subtotal')) {
            $shippingFee = config('app.shipping_fee');
        }

        $vatPrice = $cartSubtotal * config('app.vat_rate');

        return [
            'cartCount' => $cartCount,
            'cartSubtotal' => number_format($cartSubtotal, 2),
            'vatPrice' => number_format($vatPrice, 2),
            'shippingFee' =>$shippingFee,
            'cartTotal' => number_format($cartSubtotal + $shippingFee, 2),
        ];
    }  
}