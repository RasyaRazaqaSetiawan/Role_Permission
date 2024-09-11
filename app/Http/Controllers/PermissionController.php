<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB facade for transactions
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // this method will show permissions page
    public function index()
    {
        $permissions = Permission::orderBy('created_at', 'DESC')->paginate(10);
        return view('permissions.index', [
            'permissions' => $permissions
        ]);
    }

    // this method will create permission page
    public function create()
    {
        return view('permissions.create');
    }

    // this method will insert permission in DB
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3'
        ]);

        if ($validator->passes()) {
            try {
                DB::beginTransaction(); // Start transaction

                $permission = Permission::create(['name' => $request->name]);

                DB::commit(); // Commit transaction

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Permission added successfully.',
                        'data' => $permission
                    ]);
                }

                return redirect()->route('permissions.index')->with('success', 'Permission added successfully.');
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback transaction on error

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to add permission.',
                        'error' => $e->getMessage()
                    ], 500);
                }

                return redirect()->route('permissions.create')->withErrors('Failed to add permission.');
            }
        } else {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->route('permissions.create')->withErrors($validator);
        }
    }

    // this method will show edit permission page
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', [
            'permission' => $permission
        ]);
    }

    // this method will update a permission
    public function update($id, Request $request)
    {
        $permission = Permission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:permissions,name,' . $id . ',id'
        ]);

        if ($validator->passes()) {
            try {
                DB::beginTransaction(); // Start transaction

                $permission->name = $request->name;
                $permission->save();

                DB::commit(); // Commit transaction

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Permission updated successfully.',
                        'data' => $permission
                    ]);
                }

                return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback transaction on error

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update permission.',
                        'error' => $e->getMessage()
                    ], 500);
                }

                return redirect()->route('permissions.edit', $id)->withInput()->withErrors('Failed to update permission.');
            }
        } else {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->route('permissions.edit', $id)->withInput()->withErrors($validator);
        }
    }

    // this method will delete a permission in DB
    public function destroy($id)
    {
        try {
            DB::beginTransaction(); // Start transaction

            $permission = Permission::findOrFail($id);
            $permission->delete();

            DB::commit(); // Commit transaction

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Permission deleted successfully.'
                ]);
            }

            return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction on error

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete permission.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->route('permissions.index')->with('error', 'Failed to delete permission.');
        }
    }
}
