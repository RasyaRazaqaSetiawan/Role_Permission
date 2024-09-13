<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class RolePermissionController extends Controller
{
    // Menampilkan daftar role dan permission
    public function index()
    {
        $roles = Role::with('permissions')->get(); // Mengambil role beserta permissions yang dimiliki
        $permissions = Permission::all();
        return view('role_permissions.index', compact('roles', 'permissions'));
    }

    // Menambahkan hak akses ke role
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::findOrFail($request->input('role_id'));
            $permission = Permission::findOrFail($request->input('permission_id'));

            $role->givePermissionTo($permission);
            DB::commit();

            // Menambahkan pesan sukses ke session
            return redirect()->route('role_permissions.index')->with('success', 'Permission added to role successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            // Menambahkan pesan error ke session
            return redirect()->route('role_permissions.index')->with('error', 'Failed to add permission to role.');
        }
    }

    // Menghapus hak akses dari role
    public function destroy(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::findOrFail($request->input('role_id'));
            $permission = Permission::findOrFail($request->input('permission_id'));

            $role->revokePermissionTo($permission);
            DB::commit();

            // Menambahkan pesan sukses ke session
            return redirect()->route('role_permissions.index')->with('success', 'Permission removed from role successfully.');
        } catch (QueryException $e) {
            DB::rollBack();
            // Menambahkan pesan error ke session
            return redirect()->route('role_permissions.index')->with('error', 'Failed to remove permission from role.');
        }
    }
}
