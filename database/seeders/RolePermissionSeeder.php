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
        $superadmin = Role::create(['name' => 'Super Admin']);
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
                'name' => 'Dashboard',
                'description' => 'dashboard',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Settings',
                'description' => 'settings',
                'guard_name' => 'web',
            ],
            [
                'name' => 'User Management',
                'description' => 'User Management',
                'guard_name' => 'web',
            ],
        ];

        // Masukkan data permission ke dalam tabel 'permissions'
        DB::table('permissions')->insert($permissionData);

        // Ambil semua permission yang telah dimasukkan
        $permissions = Permission::all();

        // Ambil semua id dari tabel 'hakakses'
        $hakaksesIds = DB::table('hakakses')->pluck('id');

        // Berikan semua permission kepada role 'Super Admin' dan simpan di tabel 'hakakses_permission'
        foreach ($permissions as $permission) {
            $superadmin->givePermissionTo($permission->name);

            foreach ($hakaksesIds as $hakaksesId) {
                DB::table('hakakses_permission')->insert([
                    'permission_id' => $permission->id,
                    'hakakses_id' => $hakaksesId,
                    'role_id' => $superadmin->id,
                ]);
            }
        }

        // Sync role untuk user Super Admin dan Admin berdasarkan email
        $superadminUser = User::firstWhere('email', 'superadmin@gmail.com');
        $adminUser = User::firstWhere('email', 'admin@gmail.com');

        if ($superadminUser) {
            $superadminUser->syncRoles([$superadmin->id]);
        }

        if ($adminUser) {
            $adminUser->syncRoles([$admin->id]);
        }
    }
}
