<?php

namespace App\Http\Controllers;

use App\Models\CustomerInformation;
use App\Models\LineItem;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }
        
        if(!empty($cartData['productsWithoutStock'])){
            return redirect()->route('cart')->with([
                'error' => 'Some out-of-stock products have been removed from your cart.',
                'productsWithoutStock' => $cartData['productsWithoutStock']
            ]);        
        }

        $customerData = Auth::check() 
            ? Auth::user()->getCustomerData() 
            : session('guest.customer_information', []);

        return view('checkout.customer', [
            'currentStep' => $currentStep,
            'steps' => $this->steps,  
            'customerData' => $customerData,
            'cartData' => $cartData,
        ]);      
    }

    public function storeCustomerInformation (Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'floor' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'mobile' => 'required|string|max:20',
            'alternative_phone' => 'nullable|string|max:20',
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
            session()->put('guest.customer_information', $validatedData);
        }
        
        return redirect()->route('checkout.order');
    }
       
    public function order(CartService $cartService){
        $cartData = $cartService->getCartData();

        if($cartData['cart']->isEmpty()){
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        if(!empty($cartData['productsWithoutStock'])){
            return redirect()->route('cart')->with([
                'error' => 'Some out-of-stock products have been removed from your cart.',
                'productsWithoutStock' => $cartData['productsWithoutStock']
            ]);        
        }

        $customerData = [];

        if(Auth::check()){
            $customerData = Auth::user()->getCustomerData();
        } else {
            $customerData = session()->get('guest.customer_information', []);
        }

        if(empty($customerData)) {
            return redirect()->route('checkout.customer')->with('error', 'Please fill your contact and billing information.');
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
        
        $valitatedMethods = $request->validate([
            'payment_method' => ['required','string','filled','in:' . $availablePaymentMethods],

            'shipping_method' => ['required','string','filled','in:' . $availableShippingMethods],
        ]);

        $cartData = $cartService->getCartData();

        
        if($cartData['cart']->isEmpty()){
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }
        if($cartData['shipping_method'] !== $valitatedMethods['shipping_method'] || $cartData['payment_method'] !== $valitatedMethods['payment_method']){
            return redirect()->route('cart')->with('error', 'Invalid shipping/payment method');
        }

        try {
            if(Auth::check()) {
                $currentOrder = Auth::user()->currentOrder()->firstOrFail();

                DB::transaction(function () use ($currentOrder) {
                    foreach($currentOrder->lineItems as $lineItem) {
                        $product = $lineItem->product;

                        if($product->stock <= 0 || $product->stock < $lineItem->quantity) {
                            throw new \Exception('Insufficient stock for product: ' . $product->id);
                        }

                        $product->decrement('stock', $lineItem->quantity);
                    }
                });

                $currentOrder->update(['status' => 'completed']);

            } else {
                $guestCustomerInformation = session('guest.customer_information');

                $validatedCustomerData = Validator::make($guestCustomerInformation, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'address' => 'required|string|max:255',
                    'postal_code' => 'required|string|max:20',
                    'floor' => 'nullable|string|max:100',
                    'country' => 'required|string|max:100',
                    'city' => 'required|string|max:100',
                    'mobile' => 'required|string|max:20',
                    'alternative_phone' => 'nullable|string|max:20',
                ])->validate();

                DB::transaction(function () use ($cartData, $guestCustomerInformation, $validatedCustomerData){  
                    $user = User::firstOrCreate(
                        ['email' => $guestCustomerInformation['email']],
                        [
                            'name' => $guestCustomerInformation['name'],
                            'password' => 123213,
                            'is_guest' => true,
                            'admin' => false,
                        ]
                    );

                    if(!$user) {
                        return redirect('/')->with(['error' => 'Something went wrong with the completion of order']);
                    }

                    CustomerInformation::updateOrCreate(
                        ['user_id' => $user->id],                    
                        [
                            'address' => $validatedCustomerData['address'],
                            'postal_code' => $validatedCustomerData['postal_code'],
                            'floor' => $validatedCustomerData['floor'] ?? '',
                            'country' => $validatedCustomerData['country'],
                            'city' => $validatedCustomerData['city'],
                            'mobile' => $validatedCustomerData['mobile'],
                            'alternative_phone' => $validatedCustomerData['alternative_phone'] ?? '',
                        ]
                    );

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
                    ]);


                    $lineItemsToInsert = [];
                    foreach($cartData['cart'] as $item) {
                        $product = $item->product;

                        if($item->quantity <= 0){
                            throw new \Exception('Insufficient quantity for product: ' . $product->title);
                        }

                        if($product->stock < $item->quantity){
                            throw new \Exception('Insufficient stock for product: ' . $product->title);
                        }
                        
                        $lineItemsToInsert[] = [
                            'order_id' => $order->id,
                            'product_id' => $item->product->id,
                            'quantity' => $item->quantity ,
                            'price' => $item->price,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        $product->decrement('stock', $item->quantity);
                    }

                    LineItem::insert($lineItemsToInsert);
                });

                session()->forget(['guest.cart', 'guest.shipping_method', 'guest.payment_method']);
            }

        return redirect(route('order.success'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order completion error: ' . $e->getMessage());
            return redirect()->route('cart')->with('error', $e->getMessage());
        }

    }

    public function orderSuccess() {
        return view('checkout.order-success');
    }
}
