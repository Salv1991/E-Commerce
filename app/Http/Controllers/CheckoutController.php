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
            if(Auth::user()->currentOrder()?->first()->lineItems()?->count()) {     
                return redirect()->route('checkout.customer');
            };
            
            return redirect()->route('cart');
        }

        return view('checkout.login', [
            'currentStep' => $currentStep,
            'steps' => $this->steps,
        ]);
    }

    public function customer(CartService $cartService) {
        $currentStep = 3;
        $cartData = $cartService->getCartData();
        $customerData = [];

        if(Auth::check()){
            $customerData = Auth::user()->getCustomerData();

        } else if (session()->has('guest_customer_information')){
            $customerData = session()->get('guest_customer_information');
        }

        return view('checkout.customer', [
            'currentStep' => $currentStep,
            'steps' => $this->steps,  
            'customerData' => $customerData,
            'cartData' => $cartData,
        ]);      
    }

    public function storeCustomerInformation (Request $request){
        $validatedData = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'postal_code' => 'required|string',
            'floor' => 'nullable|string',
            'country' => ['required', 'string'],
            'city' => ['required', 'string'],
            'mobile' => ['required', 'string'],
            'alternative_phone' => 'nullable|string',
        ]);

        if(Auth::check()) {
            $user = Auth::user();

            if ($validatedData['email'] !== $user->email) {
                abort(403, "Email mismatch.");
            }

            $user->customerInformation()->updateOrCreate(
                ['user_id' => $user->id],
                $validatedData
            );
        } else {
            session()->put('guest_customer_information', $validatedData);
        }
        
        return redirect()->route('checkout.order');
    }
       
    public function order(CartService $cartService){
        $cartData = $cartService->getCartData();

        $customerData = [];

        if(Auth::check()){
            $customerData = Auth::user()->getCustomerData();

        } else if (session()->has('guest_customer_information')){
            $customerData = session()->get('guest_customer_information');
        }

        return view('checkout.order', [
            'currentStep' => 4,
            'steps' => $this->steps,
            'cartData' => $cartData,
            'customerData' => $customerData,
        ]);
    }

    public function updateShippingMethod(CartService $cartService, Request $request) {
        $selectedShippingMethod = $request->input('shipping_method');
        $shippingMethods = config('app.shipping_methods');

        $request->validate([
            'shipping_method' => 'required|string',
        ]);

        if(request()->ajax()){

            if( !isset($shippingMethods[$selectedShippingMethod]) ) {
                return response()->json(['error' => 'Not available shipping method.']);
            }

            if(Auth::check()) {
                $currentOrder = Auth::user()->currentOrder()->first();

                $shippingFee = $currentOrder->subtotal < config('app.free_shipping_min_subtotal') 
                    ? $shippingMethods[$selectedShippingMethod]['extra_cost']
                    : 0;

                $currentOrder->update([
                    'total_price' => $currentOrder->subtotal + $shippingFee + $currentOrder->payment_fee,
                    'shipping_method' => $selectedShippingMethod,
                    'shipping_fee' => $shippingFee,
                ]);

                $orderSummary = $cartService->calculateOrderSummaryForUser($currentOrder);
    
            } else {                    
                session()->put('guest_shipping_method', [
                    'shipping_method' => $selectedShippingMethod,
                    'extra_cost' => $shippingMethods[$selectedShippingMethod]['extra_cost']    
                ]);
                
                $orderSummary = $cartService->calculateOrderSummaryForGuest(session()->get('cart', []));
            }

            return response()->json([
                'cartCount' => $orderSummary['cartCount'],
                'cartSubtotal' => $orderSummary['cartSubtotal'],
                'cartTotal' => $orderSummary['cartTotal'], 
                'shippingFee' => $orderSummary['shippingFee'], 
            ]);
        }
    }

    public function updatePaymentMethod(CartService $cartService, Request $request) {
        $selectedPaymentMethod = $request->input('payment_method');
        $paymentMethods = config('app.payment_methods');

        $request->validate([
            'payment_method' => 'required|string',
        ]);

        if(request()->ajax()){

            if( !isset($paymentMethods[$selectedPaymentMethod]) ) {
                return response()->json(['error' => 'Not available shipping method.']);
            }

            if(Auth::check()) {
                $currentOrder = Auth::user()->currentOrder()->first();

                $currentOrder->update([
                    'total_price' => $currentOrder->subtotal + $paymentMethods[$selectedPaymentMethod]['extra_cost'] + $currentOrder->shipping_fee,
                    'payment_method' => $selectedPaymentMethod,
                    'payment_fee' => $paymentMethods[$selectedPaymentMethod]['extra_cost'],
                ]);
                
                $orderSummary = $cartService->calculateOrderSummaryForUser($currentOrder);
    
            } else {                    
                session()->put('guest_payment_method', [
                    'payment_method' => $selectedPaymentMethod,
                    'extra_cost' => $paymentMethods[$selectedPaymentMethod]['extra_cost']    
                ]);
                
                $orderSummary = $cartService->calculateOrderSummaryForGuest(session()->get('cart', []));
            }

            return response()->json([
                'cartCount' => $orderSummary['cartCount'],
                'cartSubtotal' => $orderSummary['cartSubtotal'],
                'cartTotal' => $orderSummary['cartTotal'], 
                'shippingFee' => $orderSummary['shippingFee'], 
                'paymentFee' => $orderSummary['paymentFee'], 
            ]);
        }
    }

    public function completeOrder(CartService $cartService, Request $request) {
        $cartData = $cartService->getCartData();

        $availableShippingMethods = implode( ',', array_keys( config('app.shipping_methods') ));
        $availablePaymentMethods = implode( ',', array_keys( config('app.payment_methods') ));
        
        $validatedData = $request->validate([
            'payment_method' =>  'required|in:' . $availablePaymentMethods,
            'shipping_method' => 'required|in:' . $availableShippingMethods, 
        ]);

        dd($request->all());
    }
}
