<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function show() {
        $wishlistedProducts = Auth::user()->wishlistedProducts()->get();
        return view('wishlist', compact('wishlistedProducts'));
    }

    public function toggle($id) {
       
        $product = Product::findOrFail($id);

        if (!$product->isWishlistedByUser()) {
            $status = 'added';

            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $id,
            ]);

            $isWishlisted = true;
        } else {
            $status = 'removed';

            $wishlist_item = Wishlist::where('product_id', $id)
                                    ->where('user_id', Auth::id())
                                    ->first();

            if ($wishlist_item) {
                $product_title = $wishlist_item->product->title;
                $wishlist_item->delete();
                $isWishlisted = false;
            }

        }

        if ( request()->ajax() ) {
            $viewPath = request('viewType') === 'show' ? 'components.form.wishlist-toggle-alternative' : 'components.form.wishlist-toggle';
            $formHtml = view($viewPath, compact('product', 'isWishlisted'))->render();            
            $wishlistCount = Auth::user()?->refresh()->wishlistedProducts()->count();

            return response()->json([
                'status' => $status,
                'updatedWishlistCount' => $wishlistCount,
                'productId' => $id,
                'formHtml' => $formHtml,
                'isWishlisted' =>  $isWishlisted,
                'viewType' => request('viewType'),
            ]);
        }
    }
}
