<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{   
    public function index(Category $category)
    {
        $childrenCategories = Cache::remember('category_children_' . $category->id, config('cache.durations.categories'), function () use ($category) {
            return $category->load('children.children')
                ->children
                ->flatMap(fn($child) => $child->children->pluck('id')->prepend($child->id))
                ->prepend($category->id)
                ->unique()
                ->toArray();
        });

        $wishlistedProductsIds = Cache::remember('wishlisted-product-ids-' . Auth::id(), config('cache.durations.categories'), function (){
            return Auth::check() 
                ? Auth::user()->wishlistedProductsIds()->toArray()
                : []; 
        });

        $products = Product::with('images:id,product_id,image_path')
            ->whereIn('id', function($query) use ($childrenCategories) {
                $query->select('product_id')
                      ->from('category_products')
                      ->whereIn('category_id', $childrenCategories);
            })
            ->when(request()->has('discounted_products'), function($query) {
                return $query->whereNotNull('discount'); 
            })
            ->when(request()->has(['min_price_range', 'max_price_range']), function($query) {
                $min_price = (float) request()->input('min_price_range');
                $max_price = (float) request()->input('max_price_range');

                if($min_price <= $max_price) {
                    return $query->whereBetween('current_price', [$min_price, $max_price]);
                }
            })
            ->when(request()->has('sort'), function($query) {
                return $query->orderBy('title', request()->input('sort'));
            })
            ->when(request()->has('price'), function($query) {
                return $query->orderBy('current_price', request()->input('price'));
            })
            ->paginate(9);
               
        return view('product.index', compact(['products', 'category', 'wishlistedProductsIds']));
    }
}
