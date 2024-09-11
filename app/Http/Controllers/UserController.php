<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Menampilkan halaman daftar pengguna
    public function index()
    {
        $user = User::orderBy('created_at', 'DESC')->paginate(10);
        return view('user.index', [
            'user' => $user
        ]);
    }

    // Menampilkan halaman form create pengguna
    public function create()
    {
        $roles = Role::all(); // Mengambil semua role
        return view('user.create', compact('roles'));
    }

    // Menyimpan pengguna baru ke database
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:6|confirmed',
            'roles' => 'required|array' // Roles yang akan diberikan ke pengguna
        ]);

        if ($validator->passes()) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Berikan roles ke pengguna yang dipilih
            $user->syncRoles($request->roles);

            return redirect()->route('user.index')->with('success', 'User added successfully.');
        } else {
            return redirect()->route('user.create')->withErrors($validator);
        }
    }

    // Menampilkan halaman edit pengguna
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all(); // Ambil semua role
        return view('user.edit', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    // Memperbarui pengguna yang sudah ada
    public function update($id, Request $request)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:user,email,' . $id . ',id',
            'password' => 'nullable|min:6|confirmed',
            'roles' => 'required|array' // Roles yang diperbarui
        ]);

        if ($validator->passes()) {
            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Sinkronkan ulang roles dengan pengguna yang dipilih
            $user->syncRoles($request->roles);

            return redirect()->route('user.index')->with('success', 'User updated successfully.');
        } else {
            return redirect()->route('user.edit', $id)->withInput()->withErrors($validator);
        }
    }

    // Menghapus pengguna dari database
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.index')->with('success', 'User deleted successfully.');
    }
}
