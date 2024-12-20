<?php

namespace App\Http\Controllers;

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

    public function customer() {
        $currentStep = 3;

        return view('checkout.customer', [
            'currentStep' => $currentStep,
            'steps' => $this->steps    
        ]);
    }
}
