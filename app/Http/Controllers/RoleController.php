<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $users = User::all();
        $hakAkses = DB::table('hakakses')->get();
        // dd($hak);
        return view('role.create', [
            'permissions' => $permissions,
            'hakAkses' => $hakAkses,
            'users' => $users
        ]);
    }

    // Menyimpan role baru ke database menggunakan AJAX dan ACID
    public function store(Request $request)
    {
        // Validasi input menggunakan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
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

            return redirect()->route('role.index')->with('success', 'Role created successfully!');
        } catch (\Exception $e) {
            // Rollback jika terjadi kesalahan (ACID - Isolation)
            DB::rollBack();
            return redirect()->route('role.index')->with('error', 'Something went wrong!');
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::orderBy('name', 'ASC')->get();
        $hakAkses = DB::table('hakakses')->get();
        $users = User::all();

        return view('role.edit', [
            'role' => $role,
            'permissions' => $permissions,
            'hakAkses' => $hakAkses,
            'users' => $users,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validasi input menggunakan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
            'hakakses' => 'nullable|array',
            'hakakses.*.*' => 'exists:hakakses,id'
        ]);

        if ($validator->fails()) {
            // Kembalikan respon JSON dengan error 422 jika validasi gagal
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Mulai transaksi (ACID - Atomicity & Consistency)
            DB::beginTransaction();

            // Temukan role yang akan diupdate
            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->save();

            // Hapus semua permissions yang ada
            $role->permissions()->detach();

            // Assign permissions baru ke role
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            foreach ($permissions as $permission) {
                $role->givePermissionTo($permission);
            }

            // Commit transaksi (ACID - Durability)
            DB::commit();

            return redirect()->route('role.index')->with('success', 'Role updated successfully!');
        } catch (\Exception $e) {
            // Rollback jika terjadi kesalahan (ACID - Isolation)
            DB::rollBack();
            return redirect()->route('role.index')->with('error', 'Something went wrong!');
        }
    }
    public function destroy($id)
    {
        // Validasi ID role
        $role = Role::findOrFail($id);
    
        try {
            // Mulai transaksi (ACID - Atomicity & Consistency)
            DB::beginTransaction();
    
            // Hapus semua permissions yang terasosiasi dengan role
            $role->permissions()->detach();
    
            // Hapus role dari database
            $role->delete();
    
            // Commit transaksi (ACID - Durability)
            DB::commit();
    
            // Redirect kembali ke halaman index dengan pesan sukses
            return redirect()->route('role.index')->with('success', 'Role deleted successfully!');
        } catch (\Exception $e) {
            // Rollback jika terjadi kesalahan (ACID - Isolation)
            DB::rollBack();
    
            // Redirect kembali ke halaman index dengan pesan error
            return redirect()->route('role.index')->with('error', 'Something went wrong!');
        }
    }
    
}
