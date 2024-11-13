<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product) {
        $product->load(['images']);
        $wishlistedProductsIds = auth()->check()
            ? auth()->user()->wishlistedProducts()->pluck('product_id')->toArray()
            : [];

        return view('product.show', compact(['product', 'wishlistedProductsIds']));
    }
}
