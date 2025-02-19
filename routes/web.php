<?php

use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', [SearchController::class, 'show'])->name('search.show');

Route::get('/products/search', [SearchController::class, 'search'])->name('search');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('product');

Route::get('/categories/{category}', [CategoryController::class, 'index'])->name('category');

Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login.show');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/signup', [SignupController::class, 'show'])->middleware('guest')->name('signup');
Route::post('/signup', [SignupController::class, 'create'])->middleware('guest')->name('signup.create');

Route::get('/user/orders', [UserController::class, 'orders'])->middleware('auth')->name('user.orders');
Route::get('/user/settings/customer-information', [UserController::class, 'showCustomerInformation'])->middleware('auth')->name('settings.customer-information.show');
Route::patch('/user/settings/customer-information', [UserController::class, 'editCustomerInformation'])->middleware('auth')->name('settings.customer-information.edit');
Route::get('/user/settings/account', [UserController::class, 'account'])->middleware('auth')->name('settings.account.show');
Route::delete('/user/settings/account', [UserController::class, 'deleteAccount'])->middleware('auth')->name('settings.account.delete');

Route::get('/wishlist', [WishlistController::class, 'show'])->middleware('auth')->name('wishlist');
Route::post('/wishlist/{id}', [WishlistController::class, 'toggle'])->middleware('auth')->name('wishlist.toggle');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/{id}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{id}', [CartController::class, 'quantity'])->name('cart.quantity');
Route::delete('/cart/{id}', [CartController::class, 'delete'])->name('cart.delete');

Route::get('/checkout/login', [CheckoutController::class, 'login'])->name('checkout.login');
Route::get('/checkout/customer-information', [CheckoutController::class, 'customer'])->name('checkout.customer');
Route::post('/checkout/customer-information', [CheckoutController::class, 'storeCustomerInformation'])->name('checkout.customer.submit');
Route::get('/checkout/order-information', [CheckoutController::class, 'order'])->name('checkout.order');
Route::post('/checkout/payment-method', [CheckoutController::class, 'updatePaymentMethod'])->name('checkout.order.payment');
Route::post('/checkout/shipping-method', [CheckoutController::class, 'updateShippingMethod'])->name('checkout.order.shipping');
Route::post('/checkout/order-complete', [CheckoutController::class, 'completeOrder'])->name('checkout.order.complete');
Route::get('/checkout/order-success', [CheckoutController::class, 'orderSuccess'])->name('order.success');

Route::middleware(AdminMiddleware::class)->prefix('dashboard')->group(function() {
    Route::get('/product/list', [AdminProductController::class, 'showList'])->middleware(AdminMiddleware::class)->name('admin.product.index');
    Route::get('/product/edit/{id}', [AdminProductController::class, 'product'])->middleware(AdminMiddleware::class)->name('admin.product.show');
    Route::patch('/product/edit/{id}', [AdminProductController::class, 'editProduct'])->middleware(AdminMiddleware::class)->name('admin.product.edit');
    Route::get('/product/add', [AdminProductController::class, 'createProduct'])->middleware(AdminMiddleware::class)->name('admin.product.create');
    Route::post('/product/add', [AdminProductController::class, 'storeProduct'])->middleware(AdminMiddleware::class)->name('admin.product.store');

    Route::get('/category/list', [AdminCategoryController::class, 'showList'])->middleware(AdminMiddleware::class)->name('admin.category.list');
    Route::get('/category/edit/{id}', [AdminCategoryController::class, 'category'])->middleware(AdminMiddleware::class)->name('admin.category.edit.show');
    Route::patch('/category/edit/{id}', [AdminCategoryController::class, 'editCategory'])->middleware(AdminMiddleware::class)->name('admin.category.edit.store');
    Route::get('/category/add', [AdminCategoryController::class, 'createCategory'])->middleware(AdminMiddleware::class)->name('admin.category.create.show');
    Route::post('/category/add', [AdminCategoryController::class, 'storeCategory'])->middleware(AdminMiddleware::class)->name('admin.category.create.store');

    Route::get('/order/list', [AdminOrderController::class, 'showList'])->middleware(AdminMiddleware::class)->name('admin.order.list');
    Route::get('/order/edit/{id}', [AdminOrderController::class, 'order'])->middleware(AdminMiddleware::class)->name('admin.order.edit.show');
    Route::patch('/order/edit/{id}', [AdminOrderController::class, 'editOrder'])->middleware(AdminMiddleware::class)->name('admin.order.edit.store');

});