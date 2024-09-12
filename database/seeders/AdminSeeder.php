<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'User']);

        $user = User::create([
            'name' =>'Admin',
            'email' =>'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('Admin');


        $user = User::create([
            'name' =>'Super Admin',
            'email' =>'superadmin@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('Super Admin');

        $user = User::create([
            'name' =>'User',
            'email' =>'user@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('User');
    }
}
