<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductReport;
use Illuminate\Http\Request;

class ProductReportController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        return $product->reports()->with('user')->get();
    }

    public function store(Request $request, $productId)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'description' => 'nullable|string',
            'evidence' => 'nullable|string',
        ]);

        $product = Product::findOrFail($productId);
        
        // Vérifier si l'utilisateur a déjà signalé ce produit
        $existingReport = $product->reports()->where('user_id', auth()->id())->first();
        if ($existingReport) {
            return response()->json(['message' => 'You have already reported this product'], 400);
        }

        $product->reports()->create([
            'user_id' => auth()->id(),
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'evidence' => $validated['evidence'] ?? null,
            'status' => 'pending',
        ]);

        return response()->noContent();
    }

    public function update(Request $request, $reportId)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,under_review,action_taken,closed',
            'admin_comment' => 'nullable|string',
        ]);

        $report = ProductReport::findOrFail($reportId);
        $report->update($validated);

        return response()->noContent();
    }

    public function destroy($reportId)
    {
        $report = ProductReport::findOrFail($reportId);
        $report->delete();

        return response()->noContent();
    }

    public function userReports()
    {
        return auth()->user()->productReports()->with('product')->get();
    }
}
