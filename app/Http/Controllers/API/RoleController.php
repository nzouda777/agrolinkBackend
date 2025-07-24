<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        return UserRole::withCount('users')->get();
    }

    public function show($id)
    {
        return UserRole::with(['users'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|in:admin,seller,customer,moderator,support',
            'description' => 'nullable|string|max:255',
        ]);

        $role = UserRole::create($request->all());

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $role = UserRole::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:50|unique:user_roles,name,' . $id,
            'description' => 'nullable|string|max:255',
        ]);

        $role->update($request->all());

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    public function destroy($id)
    {
        $role = UserRole::findOrFail($id);
        
        // Check if role is being used by users
        if ($role->users()->exists()) {
            return response()->json([
                'message' => 'Cannot delete role as it is being used by users'
            ], 400);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }
}
