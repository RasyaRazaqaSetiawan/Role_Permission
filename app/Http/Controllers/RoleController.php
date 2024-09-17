<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    // List all roles (Read)
    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);
        return view('role.index', compact('roles'));
    }

    // Show form to create a new role (Create - Step 1)
    public function create()
    {
        $permissions = Permission::all(); // Menggunakan model Eloquent Spatie
        $hakAkses = DB::table('hakakses')->get(); // Menggunakan Query Builder
        return view('role.create', compact('permissions', 'hakAkses'));
    }

    // Store a new role in the database (Create - Step 2)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->name
            ]);

            // Attach permissions to the role
            $role->syncPermissions($request->permissions);
        });

        return response()->json(['message' => 'Role created successfully']);
    }

    // Show form to edit a role (Update - Step 1)
    public function edit($id)
    {
        $role = Role::findOrFail($id); // Menggunakan model Eloquent Spatie
        $permissions = Permission::all(); // Menggunakan model Eloquent Spatie
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return response()->json([
            'role' => $role,
            'permissions' => $rolePermissions,
            'all_permissions' => $permissions
        ]);
    }

    // Update the role in the database (Update - Step 2)
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $id) {
            $role = Role::findOrFail($id);
            $role->update(['name' => $request->name]);

            // Update permissions
            $role->syncPermissions($request->permissions);
        });

        return response()->json(['message' => 'Role updated successfully']);
    }

    // Delete a role (Delete)
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $role = Role::findOrFail($id);
            $role->permissions()->detach();
            $role->delete();
        });

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
