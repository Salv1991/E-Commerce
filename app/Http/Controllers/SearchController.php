<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function show() {
        return view('search');
    }

    public function search() {
        $query = request()->input('query');

        $products = Product::where('title', 'LIKE', '%' . $query . '%')
            ->paginate(10);
        
        return view('search', compact('products', 'query'));
    }
}
