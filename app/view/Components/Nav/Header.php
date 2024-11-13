<?php 

namespace App\View\Components\Nav;

use Illuminate\View\Component;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class Header extends Component
{
    public $wishlistCount;
    public $categories;

    public function __construct()
    {
        $this->wishlistCount = auth()->check() ? auth()->user()->wishlistedProducts()->count() : 0;
        
        $this->categories = Cache::remember('first-depth-categories', 60, function () {
            return Category::with('children')->whereNull('parent_id')->get();
        });
    }

    public function render()
    {
        return view('components.nav.header');
    }
}
