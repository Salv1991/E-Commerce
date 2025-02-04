<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function showList() {
        $categories = Category::paginate(20);
        return view('admin.categories', compact(['categories']));
    }

    public function category($id) {
        $category = Category::with('parent.parent')->findOrFail($id);
        $categories = Category::whereNull('parent_id')
                ->with(['children.children' => function ($query) {
                $query->select('id', 'title', 'parent_id');
            }])
            ->select('id', 'title', 'parent_id')
            ->get();

        return view('admin.category.edit', compact(['category', 'categories']));
    }

    public function editCategory(Request $request, $id) {

        $category = Category::findOrFail($id);

        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'weight' => 'required|integer|min:0',
        ]);
        
        $dataToUpdate = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'weight' => $validatedData['weight'],
            'slug' => Str::slug($validatedData['title'])
        ];

        if ($request->hasFile('image')) {
            $dataToUpdate['image'] = $this->createImage($request);
        } 

        $updated = $category->update($dataToUpdate);

        return redirect()->back()->with('success', 'Category updated successfully');
    }

    public function createCategory() {
        $categories = Category::whereNull('parent_id')
                ->with(['children.children' => function ($query) {
                $query->select('id', 'title', 'parent_id');
            }])
            ->select('id', 'title', 'parent_id')
            ->get();

        return view('admin.category.create', compact('categories'));
    }

    public function storeProduct(Request $request) {
        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'weight' => 'required|integer|min:0',
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

    private function calculateDiscount($originalPrice, $currentPrice) {
        return number_format((($originalPrice - $currentPrice) / $originalPrice) * 100, 0);  
    }
}
