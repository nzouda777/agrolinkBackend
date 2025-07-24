<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        return auth()->user()->wishlists()->with('items.product')->get();
    }

    public function show($id)
    {
        $wishlist = auth()->user()->wishlists()->findOrFail($id);
        return $wishlist->load('items.product');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $wishlist = auth()->user()->wishlists()->create($validated);
        return response()->json($wishlist, 201);
    }

    public function update(Request $request, $id)
    {
        $wishlist = auth()->user()->wishlists()->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $wishlist->update($validated);
        return response()->json($wishlist);
    }

    public function destroy($id)
    {
        $wishlist = auth()->user()->wishlists()->findOrFail($id);
        $wishlist->delete();
        return response()->noContent();
    }

    public function addProduct(Request $request, $wishlistId)
    {
        $wishlist = auth()->user()->wishlists()->findOrFail($wishlistId);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist->items()->create($validated);
        return response()->noContent();
    }

    public function removeProduct($wishlistId, $productId)
    {
        $wishlist = auth()->user()->wishlists()->findOrFail($wishlistId);
        $wishlist->items()->where('product_id', $productId)->delete();
        return response()->noContent();
    }
}
