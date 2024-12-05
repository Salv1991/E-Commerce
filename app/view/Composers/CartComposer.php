<?php 

namespace App\View\Composers;

use Illuminate\View\View;
use App\Services\CartService;  

class CartComposer
{
    protected  $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function compose(View $view)
    {
        $cartData = $this->cartService->getCartData();
        $cartCount = $cartData['cartCount'];
        $cartTotal = $cartData['cartTotal'];

        $view->with([
            'cart' => $cartData['cart'],
            'cartCount' => $cartCount,
            'cartTotal' => $cartTotal,
        ]);
    }
}
