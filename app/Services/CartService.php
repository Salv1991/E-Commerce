<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCartData()
    {
        $cartData = [
            'cart' => collect(),
            'cartCount' => 0,
            'cartTotal' => 0
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
                $cartData['cartTotal'] = $currentOrder->total_price;
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
                $cartData['cartTotal'] = $cartData['cart']->sum(function ($product) {
                    return $product['quantity'] * $product['product']->current_price;
                });
            }
        }

        return $cartData;
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
}
