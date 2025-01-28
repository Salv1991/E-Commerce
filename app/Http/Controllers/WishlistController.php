<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateWishlist;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
            $user->wishlistedProducts()->attach($id);
            //UpdateWishlist::dispatch($user, $id, $status);

        } else {
            $status = 'removed';
            $user->wishlistedProducts()->detach($id); 
            //UpdateWishlist::dispatch($user, $id, $status);

        }

        Cache::forget('wishlisted-product-ids');

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
