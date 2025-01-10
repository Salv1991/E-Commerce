<?php

namespace App\Services;

use App\Models\LineItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    //check if product stock is 0 and if yes  then use reject on the collection(removedProducts);
    public function getCartData()
    {
        $cartData = [
            'cart' => collect(),
            'cartCount' => 0,
            'cartSubtotal' => 0,
            'cartTotal' => 0,
            'shipping_method' => null,
            'shipping_fee' => 0,
            'payment_method' => null,
            'payment_fee' => 0,
        ];

        if (Auth::check()) {
            $currentOrder = Auth::user()->currentOrder()
                ->with('lineItems.product.images')
                ->first();

            if ($currentOrder) {
                $cartData['cart'] = $currentOrder->lineItems->map(function ($lineItem) {
                    return (object)[
                        'id' => $lineItem->id,
                        'product' => $lineItem->product,
                        'quantity' => $lineItem->quantity,
                        'price' => $lineItem->quantity * $lineItem->product->current_price,
                        'availability' => $lineItem->product->stock  > 0 ? 'In stock' : 'Out of stock',   
                    ];
                });

                $cartData['cartCount'] = $currentOrder->lineItemsQuantity();
                $cartData['cartSubtotal'] = $currentOrder->subtotal;
                $cartData['cartTotal'] = $currentOrder->total_price;
                $cartData['shipping_method'] = $currentOrder->shipping_method;
                $cartData['shipping_fee'] = $currentOrder->shipping_fee;
                $cartData['payment_method'] = $currentOrder->payment_method;
                $cartData['payment_fee'] = $currentOrder->payment_fee;
            }

        } else {
            $guestCart = collect(session()->get('cart', []));
            
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
                            'price' => $product->current_price,
                            'availability' => $product->stock  > 0 ? 'In stock' : 'Out of stock',   
                        ];
                    }
                })->filter();

                $cartData['cartCount'] = $cartData['cart']->sum('quantity');
                $cartData['cartSubtotal'] = $cartData['cart']->sum(function ($product) {
                    return $product->quantity * $product->price;
                });

                $guestShippingMethod = session('guest_shipping_method');           
                $guestPaymentMethod = session('guest_payment_method');

                $availableShippingMethods = config('app.shipping_methods'); 

                if( isset($availableShippingMethods[$guestShippingMethod['shipping_method']]) ){
                    $selectedMethod = $guestShippingMethod['shipping_method'];
                } else {
                    $selectedMethod = array_key_first($availableShippingMethods);
                }
          
                $cartData['shipping_method'] = $selectedMethod;

                if($cartData['cartSubtotal'] > 0 && $cartData['cartSubtotal'] <= config('app.free_shipping_min_subtotal')) {                
                    $cartData['shipping_fee'] = number_format($availableShippingMethods[$selectedMethod]['extra_cost'], 2);
                } else {
                    $cartData['shipping_fee'] = 0;
                }

                $availablePaymentMethods = config('app.payment_methods');     

                if(isset($availablePaymentMethods[$guestPaymentMethod['payment_method']])){
                    $selectedPaymentMethod = $guestPaymentMethod['payment_method'];
                } else {
                    $selectedPaymentMethod = array_key_first($availablePaymentMethods);
                }

                $cartData['payment_method'] = $selectedPaymentMethod ; 
                $cartData['payment_fee'] = number_format($availablePaymentMethods[$selectedPaymentMethod]['extra_cost'], 2);
                
                $cartData['cartTotal'] = $cartData['cartSubtotal'] + $cartData['shipping_fee'] + $cartData['payment_fee'];
            }
        }

        return $cartData;
    }

    public function addProductToCart($id) {
        $product = Product::find($id);
        $cartSubtotal = 0;
        $cartCount = 0;
        $lineItemExists = false;

        if(!$product){
            return ['error' => 'Product not found.'];
        }

        if(Auth::check()){
            $user = Auth::user()->load('currentOrder.lineItems');
            $currentOrder = $user->currentOrder;

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

            if(!session()->has('guest_shipping_method')){
                session()->put('guest_shipping_method', [
                    'shipping_method' => 'elta',
                    'extra_cost' => number_format(config('app.shipping_methods.elta.extra_cost'), 2),
                ]);
            }

            if(!session()->has('guest_payment_method')){
                session()->put('guest_payment_method', [
                    'payment_method' => '',
                    'extra_cost' => 0,
                ]);
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
            $user = Auth::user()->load('currentOrder');
            $currentOrder = $user->currentOrder;

            if ($currentOrder) {
                $cartCount = $currentOrder->lineItemsQuantity();
            }
        } else {
            $guestCart = collect(session()->get('cart', []));

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

    public function calculateOrderSummaryForUser($order) {
        $cartCount = $order->lineItems->sum('quantity'); 
        $cartSubtotal = $order->subtotal; 
        $cartTotal = $order->total_price; 
        $shippingFee = $order->shipping_fee;
        $paymentFee = $order->payment_fee;
        $vatPrice = $cartSubtotal * config('app.vat_rate');

        return [
            'cartCount' => $cartCount,
            'cartSubtotal' => number_format($cartSubtotal, 2),
            'vatPrice' => number_format($vatPrice, 2),
            'shippingFee' =>number_format($shippingFee, 2),
            'paymentFee' =>number_format($paymentFee, 2),
            'cartTotal' => number_format($cartTotal, 2),
        ];
    }  

    public function calculateOrderSummaryForGuest($cart) {
        $shippingFee = 0;
        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartSubtotal = array_reduce($cart, function($total, $product){
            return $total + ($product['quantity'] * $product['price']);
        }, 0);
        $selectedShippingMethod = session()->get('guest_shipping_method');
        $selectedPaymentMethod = session()->get('guest_payment_method');
        $paymentFee = $selectedPaymentMethod['extra_cost'];

        if ($cartSubtotal > 0 && $cartSubtotal < config('app.free_shipping_min_subtotal')) {
            $shippingFee = $selectedShippingMethod['extra_cost'];        
        }

        $vatPrice = $cartSubtotal * config('app.vat_rate');

        return [
            'cartCount' => $cartCount,
            'cartSubtotal' => number_format($cartSubtotal, 2),
            'vatPrice' => number_format($vatPrice, 2),
            'shippingFee' =>number_format($shippingFee, 2),
            'paymentFee' =>number_format($paymentFee, 2),
            'cartTotal' => number_format($cartSubtotal + $shippingFee + $paymentFee, 2),
        ];
    }  

    public function mergeCarts($guestCart) {
        $user = Auth::user()->load('currentOrder');
        $currentOrder = $user->currentOrder;

        if(!$currentOrder){
            $currentOrder = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'subtotal' => 0,
                'total_price' => 0,
                'adress' => null,
            ]);
        }
        
        $productIds = array_column($guestCart, 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach($guestCart as $lineItem){
            $product = $products->get($lineItem['product_id']);

            if(!$product || $product->stock <= 0){
                continue;
            }

            $existingLineItem = LineItem::where('order_id', $currentOrder->id)
                ->where('product_id', $lineItem['product_id'])
                ->first();

            if($existingLineItem){
                $existingLineItem->update([
                    'quantity' => min($existingLineItem->quantity + $lineItem['quantity'], $product->stock)    
                ]);
            } else {
                LineItem::create([
                    'order_id' => $currentOrder->id,
                    'product_id' => $lineItem['product_id'],
                    'quantity' => min($lineItem['quantity'], $product->stock),
                    'price' => $lineItem['price']
                ]);
            }
        }     
    }

}