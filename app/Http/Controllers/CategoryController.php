<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{   
    public function index(Category $category)
    {
        $wishlistProductIds = Wishlist::where('user_id', Auth::id())->pluck('product_id')->toArray();

        $childrenCategories = $category->children()
                                        ->with('children')
                                        ->get()
                                        ->flatMap(function($child) {
                                            return $child->children->pluck('id')->prepend($child->id);
                                        })
                                        ->prepend($category->id)
                                        ->unique();

        $products = CategoryProduct::whereIn('category_id', $childrenCategories)
                                            ->with(['product.images']) 
                                            ->paginate(9);

        $products->getCollection()->transform(function ($categoryProduct) use ($wishlistProductIds) {
            $product = $categoryProduct->product;

            if ($product) {
                $product->is_wishlisted = in_array($product->id, $wishlistProductIds);
                return $product;
            }

            return null; 
        })->filter();

        return view('product.index', compact(['products', 'category']));
    }
}
