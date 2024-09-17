<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

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
                ->pluck('roles.name');
        }

        return view('user.index', ['users' => $users]);
    }

    // Menampilkan form untuk membuat pengguna baru
    public function create()
    {
        $roles = DB::table('roles')->get(); // Mengambil semua role
        return view('user.create', ['roles' => $roles]);
    }

    // Menyimpan pengguna baru dengan Query Builder dan AJAX
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|exists:roles,id',
        ]);

        // Menggunakan Query Builder untuk menyimpan data
        DB::table('users')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role_id' => $validated['role'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    // Menampilkan form untuk mengedit pengguna
    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        $roles = DB::table('roles')->get();
        $userRoles = DB::table('role_user')
            ->where('user_id', $id)
            ->pluck('role_id');

        return view('user.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
        ]);
    }

    // Memperbarui pengguna yang sudah ada dengan Query Builder dan AJAX
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'roles' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Memperbarui pengguna
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($updateData);

        // Menghapus role lama dan menyimpan role baru
        DB::table('role_user')->where('user_id', $id)->delete();
        foreach ($request->roles as $roleId) {
            DB::table('role_user')->insert([
                'user_id' => $id,
                'role_id' => $roleId,
            ]);
        }

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    // Menghapus pengguna dengan Query Builder dan AJAX
    public function destroy($id)
    {
        // Menghapus role yang terkait dengan pengguna
        DB::table('role_user')->where('user_id', $id)->delete();

        // Menghapus pengguna
        DB::table('users')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
