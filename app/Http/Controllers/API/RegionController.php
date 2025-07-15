<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    public function index()
    {
        return Region::withCount('cities')->get();
    }

    public function show($id)
    {
        return Region::with(['cities'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:regions',
            'code' => 'required|string|max:10|unique:regions',
        ]);

        $region = Region::create($request->all());

        return response()->json([
            'message' => 'Region created successfully',
            'region' => $region
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100|unique:regions,name,' . $id,
            'code' => 'required|string|max:10|unique:regions,code,' . $id,
        ]);

        $region->update($request->all());

        return response()->json([
            'message' => 'Region updated successfully',
            'region' => $region
        ]);
    }

    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        
        // Check if region has cities
        if ($region->cities()->exists()) {
            return response()->json([
                'message' => 'Cannot delete region as it has cities'
            ], 400);
        }

        $region->delete();

        return response()->json([
            'message' => 'Region deleted successfully'
        ]);
    }

    public function cities($id)
    {
        $region = Region::findOrFail($id);
        return $region->cities()->get();
    }
}
