<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index() {
        $latestProducts = Product::with('images')->orderBy('created_at', 'desc')->take(8)->get();

        $randomCategories = Category::inRandomOrder()->get()->unique('id')->take(4);

        $wishlistedProductsIds = Auth::check() 
            ? Auth::user()->wishlistedProductsIds()
            : collect(); 

        return view('home', compact(['latestProducts', 'wishlistedProductsIds', 'randomCategories']));
    }


}
