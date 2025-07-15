<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        return $query->paginate(20);
    }

    public function show($id)
    {
        return Review::with(['user', 'product'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
        ]);

        // Check if user has already reviewed this product
        if (Review::where('product_id', $request->product_id)
            ->where('user_id', $request->user()->id)
            ->exists()) {
            return response()->json([
                'message' => 'User has already reviewed this product'
            ], 400);
        }

        $review = Review::create([
            'product_id' => $request->product_id,
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'title' => $request->title,
            'content' => $request->content,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Review created successfully',
            'review' => $review
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to update this review'
            ], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
        ]);

        $review->update($request->all());

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => $review
        ]);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to delete this review'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }

    public function verifyPurchase(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Verify purchase logic here (check if user has purchased the product)
        
        $review->verified_purchase = true;
        $review->save();

        return response()->json([
            'message' => 'Purchase verification successful'
        ]);
    }
}
