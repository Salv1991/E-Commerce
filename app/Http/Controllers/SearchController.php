<?php

namespace App\Http\Controllers;

use App\Models\Product;

$validSortOrders = ['asc', 'desc'];

class SearchController extends Controller
{

    public function show() {
        return view('search');
    }

    public function search() {
        $query = request()->input('query');

        $products = Product::where('title', 'LIKE', '%' . $query . '%')
            ->when(request()->has('sort'), function($query){
                if(in_array(strtolower(request()->input('sort')), ['asc', 'desc'])){
                    $query->orderBy('title', request()->input('sort'));
                }
            })
            ->paginate(10);
        
        $wishlistedProductsIds = auth()->check()
            ? auth()->user()->wishlistedProducts()->pluck('product_id')->toArray()
            : [];

        return view('search', compact('products', 'query', 'wishlistedProductsIds'));
    }
}
