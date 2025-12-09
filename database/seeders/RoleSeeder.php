<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Use firstOrCreate to avoid duplicate role errors
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $guru = Role::firstOrCreate(['name' => 'guru']);
        $murid = Role::firstOrCreate(['name' => 'murid']);

        // Create admin user if doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
            ]
        );
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole($admin);
        }

        // Create guru user if doesn't exist
        $user1 = User::firstOrCreate(
            ['email' => 'guru@example.com'],
            [
                'name' => 'Guru Satu',
                'password' => bcrypt('password'),
            ]
        );
        if (!$user1->hasRole('guru')) {
            $user1->assignRole($guru);
        }

        // Create murid user if doesn't exist
        $user2 = User::firstOrCreate(
            ['email' => 'murid@example.com'],
            [
                'name' => 'Murid Satu',
                'password' => bcrypt('password'),
            ]
        );
        if (!$user2->hasRole('murid')) {
            $user2->assignRole($murid);
        }
    }
}