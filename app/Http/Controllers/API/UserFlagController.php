<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserFlag;
use Illuminate\Http\Request;

class UserFlagController extends Controller
{
    public function index($userId)
    {
        $user = User::findOrFail($userId);
        return $user->flagsReceived()->with('reporter')->get();
    }

    public function store(Request $request, $userId)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'description' => 'nullable|string',
            'evidence' => 'nullable|string',
        ]);

        $user = User::findOrFail($userId);
        
        // Vérifier si l'utilisateur peut signaler
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot flag yourself'], 400);
        }

        // Vérifier si l'utilisateur a déjà signalé
        $existingFlag = $user->flagsReceived()->where('reporter_id', auth()->id())->first();
        if ($existingFlag) {
            return response()->json(['message' => 'You have already flagged this user'], 400);
        }

        $user->flagsReceived()->create([
            'reporter_id' => auth()->id(),
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'evidence' => $validated['evidence'] ?? null,
            'status' => 'pending',
        ]);

        return response()->noContent();
    }

    public function update(Request $request, $flagId)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,under_review,action_taken,closed',
            'admin_comment' => 'nullable|string',
        ]);

        $flag = UserFlag::findOrFail($flagId);
        $flag->update($validated);

        return response()->noContent();
    }

    public function destroy($flagId)
    {
        $flag = UserFlag::findOrFail($flagId);
        $flag->delete();

        return response()->noContent();
    }

    public function userFlags()
    {
        return auth()->user()->userFlags()->with('reportedUser')->get();
    }
}
