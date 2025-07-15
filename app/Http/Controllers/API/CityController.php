<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    public function index()
    {
        return City::with(['region'])->paginate(20);
    }

    public function show($id)
    {
        return City::with(['region', 'users'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:100|unique:cities',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
        ]);

        $city = City::create($request->all());

        return response()->json([
            'message' => 'City created successfully',
            'city' => $city
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);
        
        $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:100|unique:cities,name,' . $id,
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
        ]);

        $city->update($request->all());

        return response()->json([
            'message' => 'City updated successfully',
            'city' => $city
        ]);
    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);
        
        // Check if city has users
        if ($city->users()->exists()) {
            return response()->json([
                'message' => 'Cannot delete city as it has users'
            ], 400);
        }

        $city->delete();

        return response()->json([
            'message' => 'City deleted successfully'
        ]);
    }

    public function findByRegion($regionId)
    {
        $region = Region::findOrFail($regionId);
        return $region->cities()->get();
    }
}
