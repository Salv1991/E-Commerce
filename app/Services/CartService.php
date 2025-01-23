<?php

namespace App\Services;

use App\Models\LineItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
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
            'productsWithoutStock' => [],
        ];

        $productsWithoutStock = [];
        $lineItemIdsToDelete = [];

        if (Auth::check()) {
            $currentOrder = Auth::user()->currentOrder()
                ->with('lineItems.product.images')
                ->first();

            if ($currentOrder) {
                $cartData['cart'] = $currentOrder->lineItems->map(function ($lineItem) use(&$productsWithoutStock, &$lineItemIdsToDelete){
                    if($lineItem->product->stock <= 0) {
                        $productsWithoutStock[] = (object)[
                            'id' => $lineItem->product->id,
                            'product' => $lineItem->product,
                            'price' => $lineItem->product->current_price,
                        ];

                        $lineItemIdsToDelete[] = $lineItem->id;

                        return null;
                    }

                    return (object)[
                        'id' => $lineItem->id,
                        'product' => $lineItem->product,
                        'quantity' => $lineItem->quantity,
                        'price' => $lineItem->quantity * $lineItem->product->current_price,
                    ];
                })->filter();

                if(!empty($productsWithoutStock)) {
                    LineItem::whereIn('id', $lineItemIdsToDelete)->delete();
                    $cartData['productsWithoutStock'] = $productsWithoutStock;
                    $currentOrder->refresh()->calculateSubtotal();
                }

                $cartData['cartCount'] = $currentOrder->lineItemsQuantity();
                $cartData['cartSubtotal'] = $currentOrder->subtotal;
                $cartData['cartTotal'] = $currentOrder->total_price;
                $cartData['shipping_method'] = $currentOrder->shipping_method;
                $cartData['shipping_fee'] = $currentOrder->shipping_fee;
                $cartData['payment_method'] = $currentOrder->payment_method;
                $cartData['payment_fee'] = $currentOrder->payment_fee;
                
            }
        } else {
            $guest = collect(session()->get('guest', []));
            $guestCart = collect($guest->get('cart', []));

            if ($guestCart->isNotEmpty()) {
                $productIds = $guestCart->pluck('product_id')->unique();
                $products = Product::with('images')->whereIn('id', $productIds)->get();

                $cartData['cart'] = $products->map(function ($product) use ($guestCart, &$productsWithoutStock) {
                    if ($guestCart->has($product->id)) {
                        if ($product->stock <= 0 ){
                            $productsWithoutStock[] = (object)[
                                'id' => $product->id,
                                'product' => $product,
                                'price' => $product->current_price,
                            ];

                           $guestCart->forget($product->id);

                           return null;
                        }

                        $quantity = $guestCart->get($product->id)['quantity'];
                        return (object)[
                            'id' => $product->id,
                            'product' => $product,
                            'quantity' => $quantity,
                            'price' => $product->current_price,
                        ];
                    }
                })->filter();

                if(!empty($productsWithoutStock)){
                    $cartData['productsWithoutStock'] = $productsWithoutStock;
                    session()->put('guest.cart', $guestCart->toArray());
                }

                // Cart quantity.
                $cartData['cartCount'] = $cartData['cart']->sum('quantity');

                // Cart subtotal.
                $cartData['cartSubtotal'] = $cartData['cart']->sum(function ($product) {
                    return $product->quantity * $product->price;
                });

                // Shipping method.
                $availableShippingMethods = config('app.shipping_methods'); 

                if($guest->has('shipping_method') && isset($availableShippingMethods[$guest['shipping_method']['value']])){
                    $selectedShippingMethod = $guest['shipping_method']['value'];
                } else {
                    $selectedShippingMethod = array_key_first($availableShippingMethods);
                }

                if($cartData['cartSubtotal'] > 0 && $cartData['cartSubtotal'] <= config('app.free_shipping_min_subtotal')) {                
                    $cartData['shipping_fee'] = number_format($availableShippingMethods[$selectedShippingMethod]['extra_cost'], 2);
                } else {
                    $cartData['shipping_fee'] = 0;
                }

                $cartData['shipping_method'] = $selectedShippingMethod;

                // Payment method.
                $availablePaymentMethods = config('app.payment_methods');   
                  
                if($guest->has('payment_method') && isset($availablePaymentMethods[$guest['payment_method']['value']])){
                    $selectedPaymentMethod = $guest['payment_method']['value'];
                    $cartData['payment_fee'] = number_format($availablePaymentMethods[$selectedPaymentMethod]['extra_cost'], 2);
                } else {
                    $selectedPaymentMethod = '';
                    $cartData['payment_fee'] = 0;
                }

                $cartData['payment_method'] = $selectedPaymentMethod ; 
                
                // Cart total.
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

        if(!$product || $product->stock <= 0){
            return ['error' => 'Product not available.'];
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
                $lineItem = LineItem::create([
                    'order_id' => $currentOrder->id,
                    'product_id' => $id,
                    'quantity' => 1,
                    'price' => $product->current_price 
                ]);

            }

            $currentOrder = $currentOrder->refresh();
            $orderData = $this->calculateOrderSummaryForUser($currentOrder);         
        } else {
            $guest = session()->get('guest', []);
            $shippingMethod = $guest['shipping_method'] ?? [];
            $paymentMethod = $guest['payment_method'] ?? [];


            if(isset($guest['cart'][$id])){
                if($guest['cart'][$id]['quantity'] >= $product->stock){
                    return ['error' => 'Not enough stock available'];
                } 

                $guest['cart'][$id]['quantity']++;
                $lineItemExists = true;
            }else{
                $guest['cart'][$id] = [
                    'product_id' => $id,
                    'quantity' => 1,
                    'price' => $product->current_price,    
                ];
            }

            if(empty($shippingMethod)){
                $guest['shipping_method'] = [
                    'value' => 'elta',
                    'extra_cost' => number_format(config('app.shipping_methods.elta.extra_cost'), 2),
                ];
            }

            if(empty($paymentMethod)){
                $guest['payment_method'] = [
                    'value' => '',
                    'extra_cost' => 0,
                ];
            }
            
            session()->put('guest', $guest);

            $orderData = $this->calculateOrderSummaryForGuest($guest);         
        }

        $quantity = $lineItem->quantity ?? $guest['cart'][$id]['quantity'];

        $orderData['message'] = 'Product added to cart';
        $orderData['product_id'] = $product->id;
        $orderData['lineItemExists'] = $lineItemExists;

        if(!$lineItemExists){
            $orderData['view'] = view('components.nav.cart-teaser', compact(['product', 'quantity']))->render();           
        } else {
            $orderData['title'] = $product->title;
            $orderData['quantity'] = $quantity;
            $orderData['total'] = number_format($product->current_price * $quantity, 2);
        }

        return $orderData;
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
            $guest = collect(session()->get('guest', []));
            $guestCart = collect($guest->get('cart', []));
            
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
            $orderData = $this->calculateOrderSummaryForUser($currentOrder);        

        } else {
            $guest = session()->get('guest', []);

            $product = Product::find($id);

            if(!$product || !isset($guest['cart'][$id])){
                return ['error' => 'Product not found.'];
            }

            $quantity = min($requestQuantity, $product->stock);

            if($quantity <= 0){
                unset($guest['cart'][$id]);
            } else {
                $guest['cart'][$id]['quantity'] = $quantity;
            }
                    
            session()->put('guest', $guest);
            
            $orderData = $this->calculateOrderSummaryForGuest($guest);
        }

        $orderData['quantity'] = $quantity;
        $orderData['product_id'] = $id;

        return $orderData;
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
                $orderData = $this->calculateOrderSummaryForUser($currentOrder);         
            } else {
                return ['error' => 'Product not found.'];
            }
        } else {
            $guest = session()->get('guest', []);

            if(!isset($guest['cart'][$id])){
                return ['error' => 'Product not found.'];          
            }
        
            unset($guest['cart'][$id]);         
            session()->put('guest', $guest);

            $orderData = $this->calculateOrderSummaryForGuest($guest);           
        }

        $orderData['message'] ='Product removed from cart.';
        $orderData['product_id'] = $id;

        return $orderData;
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

    public function calculateOrderSummaryForGuest($guest) {
       
        $cartCount = array_sum(array_column($guest['cart'], 'quantity'));

        $cartSubtotal = array_reduce($guest['cart'], function($total, $product){
            return $total + ($product['quantity'] * $product['price']);
        }, 0);

        $vatPrice = $cartSubtotal * config('app.vat_rate');

        $selectedShippingMethod = $guest['shipping_method'];

        $selectedPaymentMethod = $guest['payment_method'];
        
        $shippingFee = $cartSubtotal > 0 && $cartSubtotal < config('app.free_shipping_min_subtotal')
            ? $selectedShippingMethod['extra_cost']
            : 0;
     
        $paymentFee = $cartSubtotal > 0
            ? $selectedPaymentMethod['extra_cost']
            : 0;
       
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
        
        $productIds = array_column($guestCart, 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $upsertData = [];

        foreach($guestCart as $lineItem){
            $product = $products->get($lineItem['product_id']);
            if(!$product || $product->stock <= 0){
                continue;
            }

            $existingLineItem = $currentOrder->lineItems->firstWhere('product_id', $lineItem['product_id']);

            if($existingLineItem){
                $quantity = min($existingLineItem->quantity + $lineItem['quantity'], $product->stock);    
            } else {
                $quantity = min($lineItem['quantity'], $product->stock);
            }

            $upsertData[] = [
                'order_id' => $currentOrder->id,
                'product_id' => $lineItem['product_id'],
                'quantity' => $quantity,
                'price' => $lineItem['price']
            ];
        } 

        if (!empty($upsertData)){
            LineItem::upsert(
                $upsertData,
                uniqueBy: ['order_id', 'product_id'],
                update: ['quantity', 'price'],
            );

            $currentOrder->refresh()->calculateSubtotal();
        }
    }

}