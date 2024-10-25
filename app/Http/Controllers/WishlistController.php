<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function show() {
        $wishlistItems = Auth::user()->wishlistItems;
        $wishlistItems->load('product');

        return view('wishlist', compact('wishlistItems'));
    }

    
    public function toggle($id) {
       
        $product = Product::findOrFail($id);

        if (!$product->isWishlisted()) {
            $status = 'added';

            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $id,
            ]);
       
        } else {
            $status = 'removed';
            $wishlist_item = Wishlist::where('product_id', $id)
                                    ->where('user_id', Auth::id())
                                    ->first();

            if ($wishlist_item) {
                $product_title = $wishlist_item->product->title;
                $wishlist_item->delete();

            }

        }

        $isWishlisted = $product->isWishlisted();

        if ( request()->ajax() ) {
            $formHtml = view('components.form.wishlist-toggle', compact('product', 'isWishlisted'))->render();
            $wishlistCount = Auth::user()?->wishlistItems()->count();

            return response()->json([
                'status' => $status,
                'newCount' => $wishlistCount,
                'message' => $product->title . ' has been added to your wishlist.',
                'productId' => $id,
                'formHtml' => $formHtml,
                'isWishlisted' =>  $product->isWishlisted(),
                'userLoggedIn' => Auth::user(),
            ]);
        }
    }

    public function create($id) {
        
        $product = Product::findOrFail($id);

        $isWishlisted = true;
        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $id,
        ]);

        if (request()->ajax()) {
            $wishlistCount = Auth::user()->wishlistItems()->count();
            $formHtml = view('components.form.wishlist-add', compact('product', 'isWishlisted'))->render();

            return response()->json([
                'status' => 'added',
                'newCount' => $wishlistCount,
                'message' => $product->title . ' has been added to your wishlist.',
                'productId' => $id, // Send back the product ID
                'formHtml' => $formHtml,

            ]);
        }

        
        return redirect()->back()->with('success', $product->title . ' has been added to your wishlist.');
    }

    public function destroy($id) {
        $product = Product::findOrFail($id);
        $isWishlisted = false;

        $wishlist_item = Wishlist::where('product_id', $id)
                                ->where('user_id', Auth::id())
                                ->first();

        if ($wishlist_item) {
            $product_title = $wishlist_item->product->title;
            $wishlist_item->delete();

            if (request()->ajax()) {
                $wishlistCount = Auth::user()->wishlistItems()->count(); 
                $formHtml = view('components.form.wishlist-toggle', compact('product', 'isWishlisted'))->render();

                return response()->json([
                    'status' => 'removed',
                    'newCount' => $wishlistCount,
                    'message' => $product_title . ' has been removed from your wishlist.',
                    'productId' => $id, // Send back the product ID
                    'formHtml' => $formHtml,

                ]);
            }

            return redirect()->back()->with('success', $product_title . ' has been removed from your wishlist.');
        }

        return redirect()->back()->with('error', 'Unable to find item in your wishlist.');
    }


}
