<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserReport;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function index($userId)
    {
        $user = User::findOrFail($userId);
        return $user->reportsReceived()->with('reporter')->get();
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
            return response()->json(['message' => 'Cannot report yourself'], 400);
        }

        // Vérifier si l'utilisateur a déjà signalé
        $existingReport = $user->reportsReceived()->where('reporter_id', auth()->id())->first();
        if ($existingReport) {
            return response()->json(['message' => 'You have already reported this user'], 400);
        }

        $user->reportsReceived()->create([
            'reporter_id' => auth()->id(),
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

        $report = UserReport::findOrFail($reportId);
        $report->update($validated);

        return response()->noContent();
    }

    public function destroy($reportId)
    {
        $report = UserReport::findOrFail($reportId);
        $report->delete();

        return response()->noContent();
    }

    public function userReports()
    {
        return auth()->user()->userReports()->with('reportedUser')->get();
    }
}
