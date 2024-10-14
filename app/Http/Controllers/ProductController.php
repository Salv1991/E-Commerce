<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index() {

        $products = Product::with(['image','images'])->paginate(12);
        
        return view('product.index', compact('products'));
    }

    public function show(Product $product) {
        $product->load(['image', 'images']);
        return view('product.show', compact('product'));
    }
}
