<?php 

namespace App\View\Components\Nav;

use Illuminate\View\Component;
use App\Models\Category;

class Header extends Component
{
    public $categories;

    public function __construct()
    {
        $this->categories = Category::with('children')->whereNull('parent_id')->get();
    }

    public function render()
    {
        return view('components.nav.header');
    }
}
