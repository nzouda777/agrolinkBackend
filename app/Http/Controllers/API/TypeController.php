<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypeController extends Controller
{
    public function index()
    {
        return UserType::withCount('users')->get();
    }

    public function show($id)
    {
        return UserType::with(['users'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:user_types',
            'description' => 'nullable|string|max:255',
        ]);

        $type = UserType::create($request->all());

        return response()->json([
            'message' => 'Type created successfully',
            'type' => $type
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $type = UserType::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:50|unique:user_types,name,' . $id,
            'description' => 'nullable|string|max:255',
        ]);

        $type->update($request->all());

        return response()->json([
            'message' => 'Type updated successfully',
            'type' => $type
        ]);
    }

    public function destroy($id)
    {
        $type = UserType::findOrFail($id);
        
        // Check if type is being used by users
        if ($type->users()->exists()) {
            return response()->json([
                'message' => 'Cannot delete type as it is being used by users'
            ], 400);
        }

        $type->delete();

        return response()->json([
            'message' => 'Type deleted successfully'
        ]);
    }
}
