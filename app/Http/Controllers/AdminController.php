<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function showProducts() {
        $products = Product::get();
        return view('admin.products', compact(['products']));
    }

    public function product($id) {
        $product = Product::findOrFail($id);
        $product->load('images');

        return view('admin.product.edit', compact(['product']));
    }

    public function editProduct(Request $request, $id) {

        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'current_price' => 'required|numeric',
            'original_price' => 'required|numeric|gte:current_price',
            'stock' => 'required|integer',
        ]);
        $discount = (($validatedData['original_price'] - $validatedData['current_price']) / $validatedData['original_price']) * 100;
        $product->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'current_price' => $validatedData['current_price'],
            'original_price' => $validatedData['original_price'],
            'discount' => $discount,
            'stock' => $validatedData['stock'],    
        ]);

        return redirect()->back()->with('success', 'Product updated successfully');
    }
}
