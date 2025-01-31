<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'current_price' => 'required|numeric|min:0',
            'original_price' => 'required|numeric|min:0|gte:current_price',
            'stock' => 'required|integer',
            'mpn' => 'required|string|min:5|max:16',
        ]);

        $discount = (($validatedData['original_price'] - $validatedData['current_price']) / $validatedData['original_price']) * 100;
        
        if ($request->hasFile('image')) {
            $imagePath = $this->createImage($request);
            $product->images()->first()->update(['image_path' => $imagePath]);
        }

        $product->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'current_price' => $validatedData['current_price'],
            'original_price' => $validatedData['original_price'],
            'discount' => $discount,
            'stock' => $validatedData['stock'],
            'mpn' => $validatedData['mpn']    
        ]);

        return redirect()->back()->with('success', 'Product updated successfully');
    }

    public function createProduct() {
        
        return view('admin.product.create');
    }

    public function storeProduct(Request $request) {
        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'current_price' => 'required|numeric|min:0',
            'original_price' => 'required|numeric|min:0|gte:current_price',
            'stock' => 'required|integer',
            'mpn' => 'required|string|min:5|max:16',
        ]);

        $discount = (($validatedData['original_price'] - $validatedData['current_price']) / $validatedData['original_price']) * 100;

        $imagePath = $this->createImage($request);

        $product = Product::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'current_price' => $validatedData['current_price'],
            'original_price' => $validatedData['original_price'],
            'discount' => $discount,
            'stock' => $validatedData['stock'],  
            'mpn' => $validatedData['mpn']
        ]);

        ProductImage::create([
           'product_id' => $product->id,
           'image_path' => $imagePath
        ]);

        return redirect()->back()->with('success', 'Product added successfully');
        
    }

    private function createImage($request) {
        $file = $request->file('image');

        // Get filename without extension.
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); 

        // Get file extension.
        $extension = $file->getClientOriginalExtension(); 

        $directory = 'products/'; 

        // Start with original name.
        $filename = $originalName . '.' . $extension; 
        $counter = 1;

        // Check if file exists, and modify filename if necessary.
        while (Storage::disk('public')->exists($directory . $filename)) {
            $filename = $originalName . '-' . $counter . '.' . $extension;
            $counter++;
        }

        // Store the file with the unique filename.
        return $file->storeAs($directory, $filename, 'public');
    }
}
