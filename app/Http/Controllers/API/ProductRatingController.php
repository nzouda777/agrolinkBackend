<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductRating;
use Illuminate\Http\Request;

class ProductRatingController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        return $product->ratings()->with('user')->get();
    }

    public function store(Request $request, $productId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $product = Product::findOrFail($productId);
        
        // Vérifier si l'utilisateur a déjà évalué ce produit
        $existingRating = $product->ratings()->where('user_id', auth()->id())->first();
        if ($existingRating) {
            return response()->json(['message' => 'You have already rated this product'], 400);
        }

        $product->ratings()->create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
        ]);

        return response()->noContent();
    }

    public function update(Request $request, $productId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $product = Product::findOrFail($productId);
        
        $rating = $product->ratings()->where('user_id', auth()->id())->firstOrFail();
        $rating->update($validated);

        return response()->noContent();
    }

    public function destroy($productId)
    {
        $product = Product::findOrFail($productId);
        
        $rating = $product->ratings()->where('user_id', auth()->id())->firstOrFail();
        $rating->delete();

        return response()->noContent();
    }

    public function averageRating($productId)
    {
        $product = Product::findOrFail($productId);
        return response()->json([
            'average_rating' => $product->ratings()->avg('rating'),
            'total_ratings' => $product->ratings()->count(),
        ]);
    }
}
