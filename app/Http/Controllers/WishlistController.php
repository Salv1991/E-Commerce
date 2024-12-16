<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function show() {
        $wishlistedProducts = Auth::user()->wishlistedProducts()->with('images')->get();
        return view('wishlist', compact('wishlistedProducts'));
    }

    public function toggle($id) {
        $userId = Auth::id();
        $product = Product::findOrFail($id);

        $wishlistedProduct = Wishlist::where('user_id', $userId)
            ->where('product_id', $id)
            ->first();

        if (!$wishlistedProduct) {
            $status = 'added';

            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $id,
            ]);

            $isWishlisted = true;
        } else {
            $status = 'removed';
            $wishlistedProduct->delete();
            $isWishlisted = false;     
        }

        if (request()->ajax()) {
            $wishlistCount = Auth::user()->wishlistedProducts()->count();

            return response()->json([
                'productId' => $id,
                'status' => $status,
                'updatedWishlistCount' => $wishlistCount,
                'isWishlisted' =>  $isWishlisted,
            ]);
        }
    }
}
