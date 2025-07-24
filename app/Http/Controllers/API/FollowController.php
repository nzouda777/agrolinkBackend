<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserFollow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow($userId)
    {
        $follower = auth()->user();
        $following = User::findOrFail($userId);

        if ($follower->id === $following->id) {
            return response()->json(['message' => 'Cannot follow yourself'], 400);
        }

        $follower->following()->attach($following->id);
        return response()->noContent();
    }

    public function unfollow($userId)
    {
        $follower = auth()->user();
        $following = User::findOrFail($userId);

        $follower->following()->detach($following->id);
        return response()->noContent();
    }

    public function followers($userId)
    {
        $user = User::findOrFail($userId);
        return $user->followers()->with('following')->get();
    }

    public function following($userId)
    {
        $user = User::findOrFail($userId);
        return $user->following()->with('follower')->get();
    }

    public function checkFollow($userId)
    {
        $user = User::findOrFail($userId);
        return response()->json([
            'following' => auth()->user()->following()->where('following_id', $userId)->exists(),
        ]);
    }
}
