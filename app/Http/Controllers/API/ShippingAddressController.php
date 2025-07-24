<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;

class ShippingAddressController extends Controller
{
    public function index()
    {
        return auth()->user()->shippingAddresses()->get();
    }

    public function show($id)
    {
        $address = auth()->user()->shippingAddresses()->findOrFail($id);
        return $address->load('city.region');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'address_line1' => 'required|string',
            'address_line2' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        $address = auth()->user()->shippingAddresses()->create($validated);
        return response()->json($address->load('city.region'), 201);
    }

    public function update(Request $request, $id)
    {
        $address = auth()->user()->shippingAddresses()->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'address_line1' => 'required|string',
            'address_line2' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        $address->update($validated);
        return response()->json($address->load('city.region'));
    }

    public function destroy($id)
    {
        $address = auth()->user()->shippingAddresses()->findOrFail($id);
        $address->delete();
        return response()->noContent();
    }

    public function setDefault($id)
    {
        $address = auth()->user()->shippingAddresses()->findOrFail($id);
        
        auth()->user()->shippingAddresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->noContent();
    }
}
