<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Menampilkan daftar pengguna
    public function index()
    {
        $users = DB::table('users')
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        // Mengambil roles untuk setiap user
        foreach ($users as $user) {
            $user->roles = DB::table('role_user')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('role_user.user_id', $user->id)
                ->pluck('role.name');
        }

        return view('user.index', ['users' => $users]);
    }

    // Menampilkan form untuk membuat pengguna baru
    public function create()
    {
        $roles = DB::table('roles')->get(); // Mengambil semua role
        return view('user.create', ['roles' => $roles]);
    }

    // Menyimpan pengguna baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'roles' => 'required|array'
        ]);

        if ($validator->passes()) {
            // Menyimpan pengguna baru
            $userId = DB::table('users')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Menyimpan role pengguna
            foreach ($request->roles as $roleId) {
                DB::table('role_user')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId
                ]);
            }

            return redirect()->route('user.index')->with('success', 'User added successfully.');
        } else {
            return redirect()->route('user.create')->withErrors($validator)->withInput();
        }
    }

    // Menampilkan form untuk mengedit pengguna
    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        $roles = DB::table('roles')->get(); // Mengambil semua role
        $userRoles = DB::table('role_user')
            ->where('user_id', $id)
            ->pluck('role_id');

        return view('user.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles
        ]);
    }

    // Memperbarui pengguna yang sudah ada
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'roles' => 'required|array'
        ]);

        if ($validator->passes()) {
            // Memperbarui pengguna
            DB::table('users')->where('id', $id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->filled('password') ? Hash::make($request->password) : DB::raw('password'),
                'updated_at' => now()
            ]);

            // Menghapus role lama dan menyimpan role baru
            DB::table('role_user')->where('user_id', $id)->delete();
            foreach ($request->roles as $roleId) {
                DB::table('role_user')->insert([
                    'user_id' => $id,
                    'role_id' => $roleId
                ]);
            }

            return redirect()->route('user.index')->with('success', 'User updated successfully.');
        } else {
            return redirect()->route('user.edit', $id)->withInput()->withErrors($validator);
        }
    }

    // Menghapus pengguna
    public function destroy($id)
    {
        // Menghapus role yang terkait dengan pengguna
        DB::table('role_user')->where('user_id', $id)->delete();
        // Menghapus pengguna
        DB::table('users')->where('id', $id)->delete();

        return redirect()->route('user.index')->with('success', 'User deleted successfully.');
    }
}
