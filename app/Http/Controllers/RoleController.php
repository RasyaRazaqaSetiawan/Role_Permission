<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // Menampilkan halaman daftar role
    public function index()
    {
        $roles = Role::orderBy('created_at', 'DESC')->paginate(10);
        return view('role.index', [
            'roles' => $roles
        ]);
    }

    // Menampilkan halaman form create role
    public function create()
    {
        $permissions = Permission::orderBy('name', 'ASC')->get();
        return view('role.create', [
            'permissions' => $permissions
        ]);
    }

    // Menyimpan role baru ke database menggunakan AJAX dan ACID
    public function store(Request $request)
    {
        // Validasi input menggunakan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id' // Validasi ID permission
        ]);

        if ($validator->fails()) {
            // Kembalikan respon JSON dengan error 422 jika validasi gagal
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Mulai transaksi (ACID - Atomicity & Consistency)
            DB::beginTransaction();

            // Buat role baru
            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

            // Assign permissions ke role
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            foreach ($permissions as $permission) {
                $role->givePermissionTo($permission);
            }

            // Commit transaksi (ACID - Durability)
            DB::commit();

            return response()->json(['success' => 'Role created successfully!'], 201);
        } catch (\Exception $e) {
            // Rollback jika terjadi kesalahan (ACID - Isolation)
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
}
