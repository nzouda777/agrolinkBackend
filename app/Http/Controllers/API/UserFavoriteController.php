<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserFavorite;
use Illuminate\Http\Request;

class UserFavoriteController extends Controller
{
    public function index()
    {
        return auth()->user()->favorites()->with('product')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Vérifier si le produit est déjà en favoris
        if (auth()->user()->favorites()->where('product_id', $validated['product_id'])->exists()) {
            return response()->json(['message' => 'Product is already in favorites'], 400);
        }

        auth()->user()->favorites()->create($validated);
        return response()->noContent();
    }

    public function destroy($productId)
    {
        $favorite = auth()->user()->favorites()->where('product_id', $productId)->firstOrFail();
        $favorite->delete();
        return response()->noContent();
    }

    public function checkFavorite($productId)
    {
        return response()->json([
            'is_favorite' => auth()->user()->favorites()->where('product_id', $productId)->exists(),
        ]);
    }
}
