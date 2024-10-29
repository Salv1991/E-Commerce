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
                                     
        $products = Product::whereHas('categories', function($query) use ($childrenCategories) {
            $query->whereIn('categories.id', $childrenCategories);
        })->with('images')
        ->paginate(9);
       
        return view('product.index', compact(['products', 'category']));
    }
}
