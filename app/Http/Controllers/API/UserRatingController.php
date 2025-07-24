<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserRating;
use Illuminate\Http\Request;

class UserRatingController extends Controller
{
    public function index($userId)
    {
        $user = User::findOrFail($userId);
        return $user->ratingsReceived()->with('rater')->get();
    }

    public function store(Request $request, $userId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $user = User::findOrFail($userId);
        
        // Vérifier si l'utilisateur peut noter
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot rate yourself'], 400);
        }

        // Vérifier si l'utilisateur a déjà évalué
        $existingRating = $user->ratingsReceived()->where('rater_id', auth()->id())->first();
        if ($existingRating) {
            return response()->json(['message' => 'You have already rated this user'], 400);
        }

        $user->ratingsReceived()->create([
            'rater_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->noContent();
    }

    public function update(Request $request, $userId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $user = User::findOrFail($userId);
        
        $rating = $user->ratingsReceived()->where('rater_id', auth()->id())->firstOrFail();
        $rating->update($validated);

        return response()->noContent();
    }

    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        
        $rating = $user->ratingsReceived()->where('rater_id', auth()->id())->firstOrFail();
        $rating->delete();

        return response()->noContent();
    }

    public function averageRating($userId)
    {
        $user = User::findOrFail($userId);
        return response()->json([
            'average_rating' => $user->ratingsReceived()->avg('rating'),
            'total_ratings' => $user->ratingsReceived()->count(),
        ]);
    }
}
