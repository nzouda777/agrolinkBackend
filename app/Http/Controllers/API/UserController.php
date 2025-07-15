<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return User::with(['role', 'type', 'city', 'settings'])->paginate(20);
    }

    public function show($id)
    {
        return User::with(['role', 'type', 'city', 'settings', 'products', 'orders', 'reviews'])->findOrFail($id);
    }

    public function showAuthenticated(Request $request)
    {
        return $request->user()->load(['role', 'type', 'city', 'settings', 'products', 'orders', 'reviews']);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'string|max:255',
            'phone' => 'string|max:20|unique:users,phone,' . $request->user()->id,
            'city_id' => 'exists:cities,id',
            'bio' => 'string',
            'profile_image' => 'string',
        ]);

        $user = $request->user();
        $user->update($request->all());

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }

    public function getSettings(Request $request)
    {
        return $request->user()->settings()->firstOrCreate([
            'user_id' => $request->user()->id
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'public_profile' => 'boolean',
            'show_phone' => 'boolean',
            'show_email' => 'boolean',
            'show_exact_location' => 'boolean',
            'notification_preferences' => 'array',
        ]);

        $settings = $request->user()->settings()->firstOrCreate([
            'user_id' => $request->user()->id
        ]);

        $settings->update($request->all());

        return response()->json([
            'message' => 'Settings updated successfully',
            'settings' => $settings
        ]);
    }

    public function getSubscriptions(Request $request)
    {
        return $request->user()->subscription()->with('subscription')->get();
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string',
        ]);

        $user = $request->user();
        
        // Verify code logic here
        
        $user->email_verified = true;
        $user->save();

        return response()->json([
            'message' => 'Email verified successfully'
        ]);
    }

    public function verifyPhone(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string',
        ]);

        $user = $request->user();
        
        // Verify code logic here
        
        $user->phone_verified = true;
        $user->save();

        return response()->json([
            'message' => 'Phone verified successfully'
        ]);
    }
}
