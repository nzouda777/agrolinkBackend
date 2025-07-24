<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return auth()->user()->notifications()->latest()->get();
    }

    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        return $notification;
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->noContent();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->noContent();
    }

    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        return response()->noContent();
    }

    public function countUnread()
    {
        return response()->json([
            'unread_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    public function types()
    {
        return response()->json([
            'types' => [
                'order' => 'Order Updates',
                'review' => 'Product Reviews',
                'message' => 'Messages',
                'product' => 'Product Updates',
                'rating' => 'User Ratings',
                'flag' => 'Flag Notifications',
                'report' => 'Report Notifications',
            ]
        ]);
    }

    public function filter(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|string',
            'read' => 'nullable|boolean',
            'limit' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $query = auth()->user()->notifications();

        if ($validated['type']) {
            $query->where('type', $validated['type']);
        }

        if ($validated['read'] !== null) {
            $query->when($validated['read'], function ($query) {
                $query->whereNotNull('read_at');
            }, function ($query) {
                $query->whereNull('read_at');
            });
        }

        return $query->latest()
            ->paginate($validated['limit'] ?? 15)
            ->withQueryString();
    }
}
