<?php 

namespace App\View\Components\Nav;

use Illuminate\View\Component;
use App\Models\Category;

class Header extends Component
{
    public $categories;

    public function __construct()
    {
        $this->categories = Category::all();
    }

    public function render()
    {
        return view('components.nav.header');
    }
}
