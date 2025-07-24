<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductFlag;
use Illuminate\Http\Request;

class ProductFlagController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        return $product->flags()->with('user')->get();
    }

    public function store(Request $request, $productId)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $product = Product::findOrFail($productId);
        
        // Vérifier si l'utilisateur a déjà signalé ce produit
        $existingFlag = $product->flags()->where('user_id', auth()->id())->first();
        if ($existingFlag) {
            return response()->json(['message' => 'You have already flagged this product'], 400);
        }

        $product->flags()->create([
            'user_id' => auth()->id(),
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return response()->noContent();
    }

    public function update(Request $request, $flagId)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,resolved,rejected',
            'comment' => 'nullable|string',
        ]);

        $flag = ProductFlag::findOrFail($flagId);
        $flag->update($validated);

        return response()->noContent();
    }

    public function destroy($flagId)
    {
        $flag = ProductFlag::findOrFail($flagId);
        $flag->delete();

        return response()->noContent();
    }

    public function userFlags()
    {
        return auth()->user()->productFlags()->with('product')->get();
    }
}
