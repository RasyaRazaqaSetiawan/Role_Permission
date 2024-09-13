<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class HakAksesController extends Controller
{
    // READ (Menampilkan daftar hak akses)
    public function index()
    {
        try {
            // Mengambil data permission
            $permissions = Permission::paginate(10);
            return view('hak_akses.index', compact('permissions'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal mengambil data hak akses: ' . $e->getMessage()]);
        }
    }

    // CREATE (Tampilkan form untuk menambah hak akses baru)
    public function create()
    {
        return view('hak_akses.create');
    }

    // STORE (Menambahkan hak akses baru) - ATOMIC
    public function store(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // ATOMICITY: Mulai transaksi
        DB::beginTransaction();
        try {
            // Buat hak akses baru
            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => 'web', // Guard default
                'description' => $request->description,
            ]);

            // Commit transaction (Durability)
            DB::commit();
            return redirect()->route('hak_akses.index')->with('success', 'Hak akses berhasil dibuat!');
        } catch (Exception $e) {
            // Rollback jika terjadi error (Consistency)
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal membuat hak akses: ' . $e->getMessage()]);
        }
    }

    // EDIT (Tampilkan form untuk mengedit hak akses)
    public function edit($id)
    {
        try {
            // Mengambil data hak akses berdasarkan ID
            $permission = Permission::findOrFail($id);
            return view('hak_akses.edit', compact('permission'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menemukan hak akses: ' . $e->getMessage()]);
        }
    }

    // UPDATE (Mengupdate hak akses yang ada) - ATOMIC
    public function update(Request $request, $id)
    {
        // Validasi data
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
            // Temukan dan update hak akses
            $permission = Permission::findOrFail($id);
            $permission->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Commit transaction (Durability)
            DB::commit();
            return redirect()->route('hak_akses.index')->with('success', 'Hak akses berhasil diperbarui!');
        } catch (Exception $e) {
            // Rollback jika terjadi error (Consistency)
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui hak akses: ' . $e->getMessage()]);
        }
    }

    // DELETE (Menghapus hak akses) - ATOMIC
    public function destroy($id)
    {
        // ATOMICITY: Mulai transaksi
        DB::beginTransaction();
        try {
            // Temukan dan hapus hak akses
            $permission = Permission::findOrFail($id);
            $permission->delete();

            // Commit transaction (Durability)
            DB::commit();
            return redirect()->route('hak_akses.index')->with('success', 'Hak akses berhasil dihapus!');
        } catch (Exception $e) {
            // Rollback jika terjadi error (Consistency)
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus hak akses: ' . $e->getMessage()]);
        }
    }
}
