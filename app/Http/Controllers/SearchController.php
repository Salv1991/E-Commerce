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
            ->when(request()->has('discounted_products'), function($query){
                return $query->whereNotNull('discount');
            })
            ->when(request()->has(['min_price_range', 'max_price_range']), function($query){
                $min_price = (float) request()->input('min_price_range');
                $max_price = (float) request()->input('max_price_range');

                if($min_price <= $max_price){
                   return $query->whereBetween('current_price', [$min_price, $max_price]);
                }
            })
            ->when(request()->has('sort'), function($query){
                $sortOrder = request()->input('sort');
                if(in_array(strtolower($sortOrder), ['asc', 'desc'])){
                    $query->orderBy('title', $sortOrder);
                }
            })
            ->when(request()->has('price'), function($query){
                $sortOrder = request()->input('price');
                if(in_array(strtolower($sortOrder), ['asc', 'desc'])){
                    $query->orderBy('current_price', $sortOrder);
                }
            })
            ->paginate(9);
        
        $wishlistedProductsIds = auth()->check()
            ? auth()->user()->wishlistedProductsIds()
            : [];

        return view('search', compact('products', 'query', 'wishlistedProductsIds'));
    }
}
