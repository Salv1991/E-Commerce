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
        $user = Auth::user();

        $isWishlisted = Wishlist::where('user_id', $user->id)
            ->where('product_id', $id)
            ->exists();

        if (!$isWishlisted) {
            $status = 'added';

            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $id,
            ]);
        } else {
            $status = 'removed';
            $user->wishlistedProducts()->detach($id);        }

        if (request()->ajax()) {
            $wishlistCount = Wishlist::where('user_id', $user->id)->count();

            return response()->json([
                'productId' => $id,
                'status' => $status,
                'updatedWishlistCount' => $wishlistCount,
                'isWishlisted' =>  $isWishlisted,
            ]);
        }
    }
}
