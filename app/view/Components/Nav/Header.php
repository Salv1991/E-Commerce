<?php 

namespace App\View\Components\Nav;

use Illuminate\View\Component;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Header extends Component
{
    public $wishlistCount;
    public $categories;

    public function __construct( protected CartService $cart)
    {    
        $this->wishlistCount = 0;

        if(Auth::check()) {
            $user = Auth::user();        
            $this->wishlistCount = $user->wishlistedProducts()->count();     
        };

        $this->categories = Cache::remember('first-depth-categories', 60, function () {
            return Category::with('children')->whereNull('parent_id')->get();
        });
    }

    public function render()
    {
        //$cartData = $this->cart->getCartData();
        return view('components.nav.header',[
            //'cart' => $cartData['cart'],
            //'cartCount' => $cartData['cartCount'],
            //'cartTotal' => $cartData['cartTotal'],
        ]);
    }
}