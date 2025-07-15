<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->favorites()->with(['product'])->paginate(20);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Check if already favorited
        if ($request->user()->favorites()->where('product_id', $request->product_id)->exists()) {
            return response()->json([
                'message' => 'Product is already in favorites'
            ], 400);
        }

        $favorite = $request->user()->favorites()->create([
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'message' => 'Product added to favorites successfully',
            'favorite' => $favorite
        ], 201);
    }

    public function destroy($id)
    {
        $favorite = $request->user()->favorites()->findOrFail($id);
        $favorite->delete();

        return response()->json([
            'message' => 'Product removed from favorites successfully'
        ]);
    }

    public function checkFavorite($productId)
    {
        return response()->json([
            'is_favorite' => $request->user()->favorites()
                ->where('product_id', $productId)
                ->exists()
        ]);
    }
}
