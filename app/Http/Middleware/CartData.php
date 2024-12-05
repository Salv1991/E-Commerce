<?php

namespace App\Http\Middleware;

use App\Models\Product;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CartData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
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
                    return [
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
                        return [
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
        view()->share('test', $cartData['cart']);
        return $next($request);
    }
}
