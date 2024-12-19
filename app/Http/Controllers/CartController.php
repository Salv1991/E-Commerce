<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService){}

    public function index(){
        $wishlistedProductsIds = collect();

        if(Auth::check()) {
            $wishlistedProductsIds = Auth::user()->wishlistedProductsIds();
        };

        $cartData = $this->cartService->getCartData();
        $cartSubtotal = $cartData['cartSubtotal'];
        $cart = $cartData['cart'];
        $shippingFee = $cartData['shippingFee'];
        return view('cart', compact(['cart', 'shippingFee', 'cartSubtotal', 'wishlistedProductsIds']));
    }

    public function add($id){
        $cartData = $this->cartService->addProductToCart($id);

        if(request()->ajax()){
            return response()->json($cartData);
        }

        return redirect()->back();
    }

    public function quantity(Request $request, $id){
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        
        $cartData = $this->cartService->updateQuantity($request->quantity, $id);

        if(request()->ajax()){
            return response()->json($cartData);
        }

        return redirect()->back()->with('success', 'Cart updated successfully');
    }

    public function delete($id){
        $cartData = $this->cartService->deleteProductFromCart($id);

        if(request()->ajax()){
            return response()->json($cartData);
        }

        return redirect()->back();
    }
   
}
