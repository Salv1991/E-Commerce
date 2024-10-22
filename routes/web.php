<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});


//Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/categories/{category}', [CategoryController::class, 'index'])->name('category');

Route::get('/products/{product}', [ProductController::class, 'show'])->name('product');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/signup', [SignupController::class, 'show'])->name('signup');
Route::post('/signup', [SignupController::class, 'create']);

Route::get('/wishlist', [WishlistController::class, 'show'])->middleware('auth')->name('wishlist');
//Route::post('/wishlist/{id}', [WishlistController::class, 'create'])->middleware('auth')->name('wishlist.create');
Route::post('/wishlist/{id}', [WishlistController::class, 'toggle'])->middleware('auth')->name('wishlist.create');
Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->middleware('auth')->name('wishlist.destroy');

Route::get('/contact', function(){
    return view('contact');
});