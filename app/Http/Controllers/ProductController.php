<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    
    public function show(Product $product) {
        $product->load(['image', 'images']);
        return view('product.show', compact('product'));
    }
}
