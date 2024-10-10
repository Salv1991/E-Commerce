<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});


Route::get('/products', function(){
    $productsfound = Product::all();
    return view('products', [
        'products' => $productsfound,    
    ]);
});

Route::get('/products/{id}', function($id){
    $productsfound = Product::find($id);
    return view('products', [
        'products' => $productsfound,    
    ]);
});

Route::get('/contact', function(){
    return view('contact');
});