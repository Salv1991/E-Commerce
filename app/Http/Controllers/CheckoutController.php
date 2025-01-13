<?php

namespace App\Http\Controllers;

use App\Models\LineItem;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        
        if($cartData['cart']->isEmpty()){
            abort(400, 'Empty cart.');
        }
        
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
            $user = Auth::user()->load('customerInformation');

            if ($validatedData['email'] !== $user->email) {
                abort(403, "Email mismatch.");
            }

            $user->customerInformation->updateOrCreate(
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

        if($cartData['cart']->isEmpty()){
            abort(400, 'Empty cart.');
        }

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
                $currentOrder = Auth::user()->currentOrder()->firstOrFail();

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
                $guest = session()->get('guest', []);

                if(isset($guest['shipping_method'])){
                    $guest['shipping_method'] = [
                        'value' => $selectedShippingMethod,
                        'extra_cost' => $shippingMethods[$selectedShippingMethod]['extra_cost']    
                    ];
                }
                
                session()->put('guest', $guest);

                $orderSummary = $cartService->calculateOrderSummaryForGuest($guest);
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
                
                $guest = session()->get('guest', []);

                if(!empty($guest['cart'])){
                    $guest['payment_method'] = [
                        'value' => $selectedPaymentMethod,
                        'extra_cost' => $paymentMethods[$selectedPaymentMethod]['extra_cost']    
                    ];
                }

                session()->put('guest', $guest);
                
                $orderSummary = $cartService->calculateOrderSummaryForGuest($guest);
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
        $availableShippingMethods = implode( ',', array_keys( config('app.shipping_methods') ));
        $availablePaymentMethods = implode( ',', array_keys( config('app.payment_methods') ));
        
        $request->validate([
            'payment_method' =>  'required|in:' . $availablePaymentMethods,
            'shipping_method' => 'required|in:' . $availableShippingMethods, 
        ]);

        $cartData = $cartService->getCartData();
        
        if($cartData['cart']->isEmpty()){
            abort(400, 'Empty cart.');
        }

        if(Auth::check()) {
            $currentOrder = Auth::user()->currentOrder()->first();
            
            $currentOrder->update([
                'status' => 'completed'
            ]) ;

        } else {
            $guestCustomerInformation = session('guest_customer_information');

            if (!isset($guestCustomerInformation['name'], $guestCustomerInformation['email'], $guestCustomerInformation['adress'])) {
                abort(400, 'Incomplete customer information.');
            }

            DB::transaction(function () use ($cartData, $guestCustomerInformation){  
                $user = User::firstOrCreate(
                    ['email' => $guestCustomerInformation['email']],
                    [
                        'name' => $guestCustomerInformation['name'],
                        'password' => 123213,
                        'is_guest' => true
                    ]
                );

                if(!$user) {
                    return redirect('/')->with(['error' => 'Something went wrong with the completion of order']);
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => 'completed',
                    'total_price' => $cartData['cartTotal'],    
                    'subtotal' => $cartData['cartSubtotal'],    
                    'discount' => 0,    
                    'payment_method' => $cartData['payment_method'],    
                    'payment_fee' => $cartData['payment_fee'],
                    'shipping_method' => $cartData['shipping_method'],    
                    'shipping_fee' => $cartData['shipping_fee'],  
                    'paid' => false,  
                    'adress' => $guestCustomerInformation['cartTotal'],  
                ]);

                $lineItemsToInsert = [];
                foreach($cartData['cart'] as $item) {
                    $lineItemsToInsert[] = [
                        'order_id' => $order->id,
                        'product_id' => $item->product->id,
                        'quantity' => $item->quantity ,
                        'price' => $item->price,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                LineItem::insert($lineItemsToInsert);
            });
        }

        return redirect('/');
    }
}
