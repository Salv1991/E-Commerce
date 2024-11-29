<?php 

namespace App\View\Components\Nav;

use Illuminate\View\Component;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Header extends Component
{
    public $cart;
    public $cartCount;
    public $wishlistCount;
    public $categories;
    public $cartTotal;

    public function __construct()
    {
        $this->cart = collect();
        $this->wishlistCount = 0;
        $this->cartCount = 0;
        $this->cartTotal = 0;

        if(Auth::check()) {
            $user = Auth::user();
            $currentOrder = $user->currentOrder()->with('lineItems.product')->first();

            if( $currentOrder ){
                $this->cart = $currentOrder->lineItems;
                $this->cartCount = $currentOrder->lineItemsQuantity();
                $this->cartTotal = $currentOrder->total_price;
            }
            
            $this->wishlistCount = $user->wishlistedProducts()->count();     
        };

        $this->categories = Cache::remember('first-depth-categories', 60, function () {
            return Category::with('children')->whereNull('parent_id')->get();
        });
    }

    public function render()
    {
        return view('components.nav.header');
    }
}
