<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Menampilkan daftar pengguna
    public function index()
    {
        $users = User::with('roles')->orderBy('created_at', 'DESC')->paginate(10);

        return view('user.index', ['users' => $users]);
    }

    // Menampilkan form untuk membuat pengguna baru
    public function create()
    {
        $roles = Role::all(); // Mengambil semua role
        return view('user.create', ['roles' => $roles]);
    }

    // Menyimpan pengguna baru dengan Eloquent
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|exists:roles,id',
        ]);

        // Menyimpan pengguna
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Menyimpan role untuk pengguna
        $user->roles()->attach($validated['role']);

        return response()->json(['message' => 'User created successfully.']);
    }


    // Menampilkan form untuk mengedit pengguna
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('user.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
        ]);
    }

    // Memperbarui pengguna yang sudah ada dengan Eloquent
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Memperbarui pengguna
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        // Menghapus role lama dan menyimpan role baru
        $user->roles()->sync($request->role);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    // Menghapus pengguna dengan Eloquent
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach(); // Menghapus role yang terkait dengan pengguna
        $user->delete(); // Menghapus pengguna

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
