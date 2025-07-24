<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        return $product->variants()->get();
    }

    public function store(Request $request, $productId)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'dimensions' => 'nullable|array',
            'dimensions.*' => 'numeric|min:0',
            'attributes' => 'nullable|array',
        ]);

        $product = Product::findOrFail($productId);
        
        $product->variants()->create($validated);
        return response()->noContent();
    }

    public function update(Request $request, $productId, $variantId)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string|unique:product_variants,sku,' . $variantId,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'dimensions' => 'nullable|array',
            'dimensions.*' => 'numeric|min:0',
            'attributes' => 'nullable|array',
        ]);

        $variant = ProductVariant::where('product_id', $productId)
            ->findOrFail($variantId);

        $variant->update($validated);
        return response()->noContent();
    }

    public function destroy($productId, $variantId)
    {
        $variant = ProductVariant::where('product_id', $productId)
            ->findOrFail($variantId);

        $variant->delete();
        return response()->noContent();
    }

    public function updateStock(Request $request, $productId, $variantId)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $variant = ProductVariant::where('product_id', $productId)
            ->findOrFail($variantId);

        $variant->update($validated);
        return response()->noContent();
    }
}
