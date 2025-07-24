<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        return Subscription::withCount('users')->get();
    }

    public function show($id)
    {
        return Subscription::with(['users'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,quarterly,yearly',
            'features' => 'required|array',
        ]);

        $subscription = Subscription::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'billing_period' => $request->billing_period,
            'features' => $request->features,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Subscription created successfully',
            'subscription' => $subscription
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,quarterly,yearly',
            'features' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $subscription->update($request->all());

        return response()->json([
            'message' => 'Subscription updated successfully',
            'subscription' => $subscription
        ]);
    }

    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        
        // Check if subscription has users
        if ($subscription->users()->exists()) {
            return response()->json([
                'message' => 'Cannot delete subscription as it has users'
            ], 400);
        }

        $subscription->delete();

        return response()->json([
            'message' => 'Subscription deleted successfully'
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'payment_method' => 'required|string',
        ]);

        $subscription = Subscription::findOrFail($request->subscription_id);
        $user = $request->user();

        // Check if user already has an active subscription
        if ($user->subscription && $user->subscription->status === 'active') {
            return response()->json([
                'message' => 'User already has an active subscription'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create new subscription
            $userSubscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'start_date' => now(),
                'end_date' => now()->addMonths(
                    $subscription->billing_period === 'monthly' ? 1 : 
                    ($subscription->billing_period === 'quarterly' ? 3 : 12)
                ),
                'status' => 'active',
                'payment_method' => $request->payment_method,
            ]);

            // Update user's subscription
            $user->subscription()->updateOrCreate([
                'user_id' => $user->id
            ], [
                'subscription_id' => $subscription->id,
                'start_date' => now(),
                'end_date' => $userSubscription->end_date,
                'status' => 'active',
                'payment_method' => $request->payment_method,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Subscription checkout successful',
                'subscription' => $userSubscription
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
