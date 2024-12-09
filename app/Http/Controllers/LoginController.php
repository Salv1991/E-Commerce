<?php

namespace App\Http\Controllers;

use App\Models\LineItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show() {
        return view('user.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $guestCart = session()->get('cart', []);

           if(!empty($guestCart)){
                $this->mergeCarts($guestCart);
           }

            $request->session()->regenerate();

            session()->flash('success', 'Welcome back, ' . Auth::user()->name . '!');

            return redirect('/');
        }

        return back()->withErrors([
            'login' => __('auth.failed')
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You logged out successfully!');  
    }

    protected function mergeCarts($guestCart) {
        $user = Auth::user();
        $currentOrder = $user->currentOrder()->first();

        if(!$currentOrder){
            $currentOrder = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_price' => 0,
                'adress' => null,
            ]);
        }
        
        $productIds = array_column($guestCart, 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach($guestCart as $lineItem){
            $product = $products->get($lineItem['product_id']);

            $existingLineItem = LineItem::where('order_id', $currentOrder->id)
                ->where('product_id', $lineItem['product_id'])
                ->first();

            if(!$product || $product->stock <= 0){
                continue;
            }

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

        session()->forget('cart');
    }
}