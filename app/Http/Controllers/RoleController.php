<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class RoleController extends Controller
{
    // List all roles (Read)
    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);
        $permissions = Permission::all();
        $hakAkses = DB::table('hakakses')->get(); // Mengambil hak akses

        return view('role.index', compact('roles', 'permissions', 'hakAkses'));
    }


    // Show form to create a new role (Create - Step 1)
    public function create()
    {
        $permissions = Permission::all();
        $hakAkses = DB::table('hakakses')->get();
        return view('role.create', compact('permissions', 'hakAkses'));
    }

  // Store a new role in the database (Create - Step 2)
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|unique:roles,name',
        'permissions' => 'required|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    DB::beginTransaction();
    try {
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);
        DB::commit();
        return response()->json(['message' => 'Role created successfully!'], 200);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Failed to create role: ' . $e->getMessage()], 500);
    }
}


    // Show form to edit a role (Update - Step 1)
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
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
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ATOMICITY: Mulai transaksi
        DB::beginTransaction();
        try {
            // Update role
            $role = Role::findOrFail($id);
            $role->update(['name' => $request->name]);

            // Update permissions
            $role->syncPermissions($request->permissions);

            // Commit transaction
            DB::commit();
            return response()->json(['message' => 'Role updated successfully!'], 200);
        } catch (Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();
            return response()->json(['error' => 'Failed to update role: ' . $e->getMessage()], 500);
        }
    }

    // Delete a role (Delete)
    public function destroy($id)
    {
        // ATOMICITY: Mulai transaksi
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            $role->permissions()->detach();
            $role->delete();

            // Commit transaction
            DB::commit();
            return response()->json(['message' => 'Role deleted successfully!'], 200);
        } catch (Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete role: ' . $e->getMessage()], 500);
        }
    }
}
