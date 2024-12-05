<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{   
    public function index(Category $category)
    {
        //Get all the current category's children's children.
        $childrenCategories = $category->children()
                                        ->get()
                                        ->flatMap(fn($child) =>  $child->children->pluck('id')->prepend($child->id))
                                        ->prepend($category->id)
                                        ->unique();
                                     
        $wishlistedProductsIds = auth()->check() 
            ? auth()->user()->wishlistedProductsIds()
            : collect(); 
        
        $productsQuery = Product::whereHas('categories', function($query) use ($childrenCategories) {
                $query->whereIn('categories.id', $childrenCategories)
                    ->when(request()->has('discounted_products'), function($query) {
                        return $query->whereNotNull('discount'); 
                    })
                    ->when(request()->has(['min_price_range', 'max_price_range']), function($query) {
                        $min_price = (float) request()->input('min_price_range');
                        $max_price = (float) request()->input('max_price_range');

                        if($min_price <= $max_price) {
                            return $query->whereBetween('current_price', [$min_price, $max_price]);
                        }
                    });
        })
        ->when(request()->has('sort'), function($query) {
            return $query->orderBy('title', request()->input('sort'));
        })
        ->when(request()->has('price'), function($query) {
            return $query->orderBy('current_price', request()->input('price'));
        })
        ->with('images');

        // Paginate products
        $products = $productsQuery->paginate(9);
               
        return view('product.index', compact(['products', 'category', 'wishlistedProductsIds']));
    }
}
