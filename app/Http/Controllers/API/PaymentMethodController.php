<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return auth()->user()->paymentMethods()->with('apiKeys')->get();
    }

    public function show($id)
    {
        $method = auth()->user()->paymentMethods()->findOrFail($id);
        return $method->load('apiKeys');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'provider' => 'required|string',
            'type' => 'required|string',
            'is_default' => 'boolean',
        ]);

        $method = auth()->user()->paymentMethods()->create($validated);
        return response()->json($method, 201);
    }

    public function update(Request $request, $id)
    {
        $method = auth()->user()->paymentMethods()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'provider' => 'required|string',
            'type' => 'required|string',
            'is_default' => 'boolean',
        ]);

        $method->update($validated);
        return response()->json($method);
    }

    public function destroy($id)
    {
        $method = auth()->user()->paymentMethods()->findOrFail($id);
        $method->delete();
        return response()->noContent();
    }

    public function setDefault($id)
    {
        $method = auth()->user()->paymentMethods()->findOrFail($id);
        
        auth()->user()->paymentMethods()->update(['is_default' => false]);
        $method->update(['is_default' => true]);

        return response()->noContent();
    }

    public function storeApiKey(Request $request, $methodId)
    {
        $method = auth()->user()->paymentMethods()->findOrFail($methodId);

        $validated = $request->validate([
            'key' => 'required|string',
            'secret' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $method->apiKeys()->create($validated);
        return response()->noContent();
    }

    public function updateApiKey(Request $request, $methodId, $apiKeyId)
    {
        $method = auth()->user()->paymentMethods()->findOrFail($methodId);
        $apiKey = $method->apiKeys()->findOrFail($apiKeyId);

        $validated = $request->validate([
            'key' => 'required|string',
            'secret' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $apiKey->update($validated);
        return response()->noContent();
    }

    public function deleteApiKey($methodId, $apiKeyId)
    {
        $method = auth()->user()->paymentMethods()->findOrFail($methodId);
        $apiKey = $method->apiKeys()->findOrFail($apiKeyId);
        $apiKey->delete();

        return response()->noContent();
    }
}
