<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // BUAT ROLE
        $admin = Role::create(['name' => 'Admin']);
        $user = Role::create(['name' => 'User']);

        // Data hak akses (permissions)
        $hakaksesData = [
            ['name' => 'create'],
            ['name' => 'read'],
            ['name' => 'update'],
            ['name' => 'delete'],
        ];

        // Masukkan data hak akses ke dalam tabel 'hakakses'
        foreach ($hakaksesData as $item) {
            DB::table('hakakses')->insert($item);
        }

        // Data permission
        $permissionData = [
            [
                'name' => 'Permissions',
                'description' => 'permissions',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Role',
                'description' => 'Role',
                'guard_name' => 'web',
            ],

            [
                'name' => 'User',
                'description' => 'User',
                'guard_name' => 'web',
            ],
        ];

        // Masukkan data permission ke dalam tabel 'permissions'
        DB::table('permissions')->insert($permissionData);

        // Ambil semua permission yang telah dimasukkan
        $permissions = Permission::all();

        // Ambil semua id dari tabel 'hakakses'
        $hakaksesIds = DB::table('hakakses')->pluck('id');

        // Berikan semua permission kepada role 'Admin' dan simpan di tabel 'hakakses_permission'
        foreach ($permissions as $permission) {
            $admin->givePermissionTo($permission->name);

            foreach ($hakaksesIds as $hakaksesId) {
                DB::table('hakakses_permission')->insert([
                    'permission_id' => $permission->id,
                    'hakakses_id' => $hakaksesId,
                    'role_id' => $admin->id,
                ]);
            }
        }

        // Sync role untuk user Super Admin dan Admin berdasarkan email
        $adminUser = User::firstWhere('email', 'admin@gmail.com');
        $adminUser = User::firstWhere('email', 'admin@gmail.com');

        if ($adminUser) {
            $adminUser->syncRoles([$admin->id]);
        }

        if ($adminUser) {
            $adminUser->syncRoles([$admin->id]);
        }
    }
}
