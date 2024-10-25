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
        //Get all the wishlisted product  and get only their ids.
        $wishlistProductIds = Wishlist::where('user_id', Auth::id())->pluck('product_id')->toArray();

        //Get all the current category's children's children.
        $childrenCategories = $category->children()
                                        ->with('children')
                                        ->get()
                                        ->flatMap(function($child) {
                                            return $child->children->pluck('id')->prepend($child->id);
                                        })
                                        ->prepend($category->id)
                                        ->unique();

        //Get all the category products with the ids in childrenCategories.
        $products = CategoryProduct::whereIn('category_id', $childrenCategories)
                                    ->with(['product.images']) 
                                    ->paginate(9);


        // Transforms the collection and adds 'is_wishlisted' property to indicate if the product is in the user's wishlist.
        $products->getCollection()->transform(function ($categoryProduct) use ($wishlistProductIds) {
            $product = $categoryProduct->product;
            $product->is_wishlisted = in_array($product->id, $wishlistProductIds);
            return $product;
        });

        return view('product.index', compact(['products', 'category']));
    }
}
