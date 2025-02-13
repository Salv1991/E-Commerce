<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\CreateImage;

class ProductController extends Controller
{
    use CreateImage;

    public function showList() {
        $products = Product::paginate(20);
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

        $discount = $this->calculateDiscount($validatedData['original_price'], $validatedData['current_price']);
        
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
        $categories = Category::whereNull('parent_id')
                ->with(['children.children' => function ($query) {
                $query->select('id', 'title', 'parent_id');
            }])
            ->select('id', 'title', 'parent_id')
            ->get();

        return view('admin.product.create', compact('categories'));
    }

    public function storeProduct(Request $request) {
        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'current_price' => 'required|numeric|min:0',
            'original_price' => 'required|numeric|min:0|gte:current_price',
            'stock' => 'required|integer',
            'mpn' => 'required|string|min:5|max:16',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $discount = $this->calculateDiscount($validatedData['original_price'], $validatedData['current_price']);

        if ($request->hasFile('image')) {
            $imagePath = $this->createImage($request);
        } else {
            $imagePath = 'products/placeholder.jpg';
        }

        $product = Product::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'current_price' => $validatedData['current_price'],
            'original_price' => $validatedData['original_price'],
            'discount' => $discount,
            'stock' => $validatedData['stock'],  
            'mpn' => $validatedData['mpn']
        ]);
        
        $product->categories()->sync($validatedData['categories']);

        ProductImage::create([
           'product_id' => $product->id,
           'image_path' => $imagePath
        ]);

        return redirect()->back()->with('success', 'Product added successfully');  
    }

    private function calculateDiscount($originalPrice, $currentPrice) {
        return number_format((($originalPrice - $currentPrice) / $originalPrice) * 100, 0);  
    }
}
