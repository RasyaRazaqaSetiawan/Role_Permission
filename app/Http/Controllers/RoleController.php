<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // Menampilkan halaman daftar role
    public function index()
    {
        $role = Role::orderBy('created_at', 'DESC')->paginate(10);
        return view('role.index', [
            'role' => $role
        ]);
    }

    // Menampilkan halaman form create role
    public function create()
    {
        $permission = Permission::orderBy('name', 'ASC')->get();
        return view('role.create', [
            'permission' => $permission
        ]);
    }

    // Menyimpan role baru ke database
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3',
            'permissions' => 'required|array' // Pastikan permissions ada
        ]);

        if ($validator->passes()) {
            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

            if (!empty($request->permissions)) {
                // Ambil permission berdasarkan ID dan berikan ke role
                $permission = Permission::whereIn('id', $request->permissions)->get();
                foreach ($permission as $name) {
                    $role->givePermissionTo($name); // Berikan permission berdasarkan nama
                }
            }

            return redirect()->route('role.index')->with('success', 'Roles added successfully.');
        }

        return redirect()->route('role.create')->withErrors($validator);
    }

    // Menampilkan halaman edit role
    public function edit($id){
        $role = Role::findOrFail($id);
        $hasPermission = $role->permissions->pluck('name');
        $permission = Permission::orderBy('name', 'ASC')->get();

        return view('role.edit',[
            'permission'=> $permission,
            'hasPermission'=> $hasPermission,
            'role'=> $role
        ]);
    }
}
