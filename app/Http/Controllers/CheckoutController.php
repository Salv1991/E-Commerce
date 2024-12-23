<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{

    private $steps;

    public function __construct()
    {
        $this->steps = [
            'Cart',
            'Login',
            'Customer Information',
            'Order Information' 
        ];
    }
    
    public function login() {
        $currentStep = 2;
        
        if(Auth::check()){
            if(Auth::user()->currentOrder()->first()->lineItems()->count()) {     
                return redirect()->route('checkout.customer');
            };
            
            return redirect()->route('cart');
        }

        return view('checkout.login', [
            'currentStep' => $currentStep,
            'steps' => $this->steps    
        ]);
    }

    public function customer(CartService $cartService) {
        $currentStep = 3;

        $cartData = $cartService->getCartData();
        $cart = $cartData['cart'];
        $cartCount = $cartData['cartCount'];

        return view('checkout.customer', [
            'currentStep' => $currentStep,
            'steps' => $this->steps,  
            'cart' => $cart,  
            'cartCount' => $cartCount,  
        ]);      
    }

    public function storeCustomerInformation (Request $request){
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'address' => ['required', 'string'],
            'postal_code' => 'required|string',
            'floor' => 'nullable|string',
            'country' => ['required', 'string'],
            'city' => ['required', 'string'],
            'mobile' => ['required', 'string'],
            'alternative_phone' => 'nullable|string',
        ]);
        
    }
        
}
