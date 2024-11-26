<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/search', [SearchController::class, 'show'])->name('search.show');

Route::get('/products/search', [SearchController::class, 'search'])->name('search');

Route::get('/categories/{category}', [CategoryController::class, 'index'])->name('category');

Route::get('/products/{product}', [ProductController::class, 'show'])->name('product');

Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login.show');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/signup', [SignupController::class, 'show'])->middleware('guest')->name('signup');
Route::post('/signup', [SignupController::class, 'create'])->middleware('guest')->name('signup.create');

Route::get('/wishlist', [WishlistController::class, 'show'])->middleware('auth')->name('wishlist');
Route::post('/wishlist/{id}', [WishlistController::class, 'toggle'])->middleware('auth')->name('wishlist.toggle');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{id}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/{id}', [CartController::class, 'delete'])->name('cart.delete');

Route::get('/contact', function(){
    return view('contact');
});