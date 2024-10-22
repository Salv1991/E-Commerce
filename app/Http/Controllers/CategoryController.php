<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{   
    public function index(Category $category){
        $wishlistProductIds = Wishlist::where('user_id', Auth::id())->pluck('product_id')->toArray();

        $products = $category->products()->with('images')->paginate(9);

        $products->transform(function ($product) use ($wishlistProductIds) {
            $product->is_wishlisted = in_array($product->id, $wishlistProductIds);
            return $product;
        });

        return view('product.index', compact(['products', 'category']));
    }
}
