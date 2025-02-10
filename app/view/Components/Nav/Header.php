<?php 

namespace App\View\Components\Nav;

use Illuminate\View\Component;
use App\Models\Category;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class Header extends Component
{
    public $wishlistCount;
    public $categories;
    public $cart;
    public $cartCount;
    public $cartTotal;
    public $isCartView;
    public $cartSubtotal;

    public function __construct(protected CartService $cartService)
    {       
        $this->cart = collect();
        $this->cartCount = 0;
        $this->cartTotal = 0;
        $this->isCartView = $this->isCartView();

        $wishlistedProducts = Cache::remember('wishlisted-product-ids-' . Auth::id(), config('cache.durations.categories'), function (){
            return Auth::check() 
                ? Auth::user()->wishlistedProductsIds()->toArray()
                : []; 
        });

        $this->wishlistCount = count($wishlistedProducts);
        
        $this->categories = Cache::remember('first-depth-categories', config('cache.durations.categories'), function () {
            return Category::whereNull('parent_id')
                ->with(['children.children' => function ($query) {
                $query->select('id', 'title', 'parent_id');
            }])
            ->select('id', 'title', 'image_path')
            ->get();
        });

        if(!$this->isCartView){
            $cartData = $this->cartService->getCartData();
            $this->cart = $cartData['cart'] ?? collect();
            $this->cartCount = $cartData['cartCount'] ?? 0;
            $this->cartSubtotal = $cartData['cartSubtotal'] ?? 0;
        } else {
            $this->cartCount = $this->cartService->getCartCount();
        }
    }

    protected function isCartView() {
        return Route::currentRouteName() === 'cart';
    }

    public function render()
    {
        return view('components.nav.header');
    }
}