<?php

namespace App\Http\Controllers;

use App\Models\HakAkses;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class PermissionController extends Controller
{
    // READ (View permissions)
    public function index()
    {
        try {
            // Mengambil data permission dengan pagination
            $permissions = Permission::paginate(10);
            return view('permissions.index', compact('permissions'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to fetch permissions: ' . $e->getMessage()]);
        }
    }

    // CREATE (View create form)
    public function create()
    {
        return view('permissions.create');
    }

    // STORE (Add a new permission) - ATOMIC
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ATOMICITY: Mulai transaksi
        DB::beginTransaction();
        try {
            // Buat permission baru
            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => 'web',
                'description' => $request->description,
            ]);

            // Commit transaction (Durability)
            DB::commit();
            return response()->json(['success' => 'Permission created successfully!'], 200);
        } catch (Exception $e) {
            // Rollback jika terjadi error (Consistency)
            DB::rollBack();
            return response()->json(['error' => 'Failed to create permission: ' . $e->getMessage()], 500);
        }
    }


    // EDIT (View edit form)
    public function edit($id)
    {
        try {
            // Mengambil data permission berdasarkan id
            $permission = Permission::findOrFail($id);
            return view('permissions.edit', compact('permission'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to fetch permission: ' . $e->getMessage()]);
        }
    }

    // UPDATE (Update an existing permission) - ATOMIC
    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // ATOMICITY: Mulai transaksi
        DB::beginTransaction();
        try {
            // Update permission
            $permission = Permission::findOrFail($id);
            $permission->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Commit transaction (Durability)
            DB::commit();
            return redirect()->route('permissions.index')->with('success', 'Permission updated successfully!');
        } catch (Exception $e) {
            // Rollback jika terjadi error (Consistency)
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to update permission: ' . $e->getMessage()]);
        }
    }

    // DELETE (Delete a permission) - ATOMIC
    public function destroy($id)
    {
        // ATOMICITY: Mulai transaksi
        DB::beginTransaction();
        try {
            // Hapus permission
            $permission = Permission::findOrFail($id);
            $permission->delete();

            // Commit transaction (Durability)
            DB::commit();
            return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully!');
        } catch (Exception $e) {
            // Rollback jika terjadi error (Consistency)
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to delete permission: ' . $e->getMessage()]);
        }
    }
}
