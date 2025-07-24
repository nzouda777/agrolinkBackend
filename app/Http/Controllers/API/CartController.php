<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function show()
    {
        $cart = auth()->user()->cart()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        return $cart->load(['items.product', 'items.variant']);
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = auth()->user()->cart()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        // Vérifier la disponibilité du variant
        $variant = ProductVariant::findOrFail($validated['variant_id']);
        if ($variant->stock < $validated['quantity']) {
            return response()->json([
                'message' => 'Insufficient stock',
                'available_stock' => $variant->stock
            ], 400);
        }

        // Ajouter ou mettre à jour l'item dans le panier
        $cartItem = $cart->items()->updateOrCreate(
            [
                'product_id' => $validated['product_id'],
                'variant_id' => $validated['variant_id'],
            ],
            [
                'quantity' => DB::raw('COALESCE(quantity, 0) + ' . $validated['quantity']),
            ]
        );

        return $cart->load(['items.product', 'items.variant']);
    }

    public function updateItem(Request $request, CartItem $item)
    {
        $this->authorize('update', $item->cart);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Vérifier la disponibilité du variant
        $variant = $item->variant;
        if ($variant->stock < $validated['quantity']) {
            return response()->json([
                'message' => 'Insufficient stock',
                'available_stock' => $variant->stock
            ], 400);
        }

        $item->update([
            'quantity' => $validated['quantity'],
        ]);

        return $item->cart()->with(['items.product', 'items.variant'])->first();
    }

    public function removeItem(CartItem $item)
    {
        $this->authorize('update', $item->cart);
        $item->delete();
        return response()->noContent();
    }

    public function clear()
    {
        $cart = auth()->user()->cart()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        $cart->items()->delete();
        return response()->noContent();
    }

    public function calculateTotal()
    {
        $cart = auth()->user()->cart()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'total' => $cart->calculateTotal(),
            'items_count' => $cart->items()->count(),
            'items' => $cart->items()->with(['product', 'variant'])->get()->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal(),
                ];
            })
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $cart = auth()->user()->cart()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        // TODO: Implémenter la logique des coupons
        // C'est un exemple simple qui applique un coupon de 10%
        if ($validated['coupon_code'] === 'DISCOUNT10') {
            $cart->coupon_code = $validated['coupon_code'];
            $cart->discount = $cart->calculateTotal() * 0.1;
            $cart->save();
            
            return response()->json([
                'message' => 'Coupon applied successfully',
                'discount' => $cart->discount,
                'new_total' => $cart->calculateTotal() - $cart->discount,
            ]);
        }

        return response()->json(['message' => 'Invalid coupon code'], 400);
    }
}
