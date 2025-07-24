<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOption;
use App\Models\DeliveryRate;
use Illuminate\Http\Request;

class DeliveryOptionController extends Controller
{
    public function index()
    {
        return DeliveryOption::with('rates.city')->get();
    }

    public function show($id)
    {
        return DeliveryOption::with('rates.city')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        $option = DeliveryOption::create($validated);

        if ($request->has('rates')) {
            foreach ($request->rates as $rate) {
                $option->rates()->create([
                    'city_id' => $rate['city_id'],
                    'base_price' => $rate['base_price'],
                    'per_km_price' => $rate['per_km_price'] ?? 0,
                ]);
            }
        }

        return response()->json($option->load('rates.city'), 201);
    }

    public function update(Request $request, $id)
    {
        $option = DeliveryOption::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        $option->update($validated);

        if ($request->has('rates')) {
            $option->rates()->delete();
            foreach ($request->rates as $rate) {
                $option->rates()->create([
                    'city_id' => $rate['city_id'],
                    'base_price' => $rate['base_price'],
                    'per_km_price' => $rate['per_km_price'] ?? 0,
                ]);
            }
        }

        return response()->json($option->load('rates.city'));
    }

    public function destroy($id)
    {
        $option = DeliveryOption::findOrFail($id);
        $option->delete();
        return response()->noContent();
    }

    public function calculateShipping(Request $request)
    {
        $validated = $request->validate([
            'option_id' => 'required|exists:delivery_options,id',
            'city_id' => 'required|exists:cities,id',
            'weight' => 'required|numeric|min:0',
            'distance' => 'required|numeric|min:0',
        ]);

        $rate = DeliveryRate::where('delivery_option_id', $validated['option_id'])
            ->where('city_id', $validated['city_id'])
            ->firstOrFail();

        $shippingCost = $rate->base_price + ($rate->per_km_price * $validated['distance']);

        return response()->json([
            'option' => $rate->deliveryOption,
            'city' => $rate->city,
            'base_price' => $rate->base_price,
            'per_km_price' => $rate->per_km_price,
            'distance' => $validated['distance'],
            'total_cost' => $shippingCost,
        ]);
    }
}
