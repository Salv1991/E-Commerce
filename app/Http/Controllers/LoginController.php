<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(protected CartService $cartService){}

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
                $this->cartService->mergeCarts($guestCart);
                session()->forget('cart');
            }

            $request->session()->regenerate();

            session()->flash('success', 'Welcome back, ' . Auth::user()->name . '!');
            
            if (url()->previous() == route('checkout.login')) {
                return redirect()->route('checkout.customer');
            }
            
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
}