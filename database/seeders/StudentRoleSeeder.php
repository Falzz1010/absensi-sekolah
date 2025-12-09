<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Murid;

class StudentRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create both student and murid roles if not exists
        $studentRole = Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web'
        ]);
        
        $muridRole = Role::firstOrCreate([
            'name' => 'murid',
            'guard_name' => 'web'
        ]);

        $this->command->info('Student and murid roles created/verified');

        // Assign both roles to all users linked to Murid
        $murids = Murid::whereNotNull('user_id')->with('user')->get();
        
        $count = 0;
        foreach ($murids as $murid) {
            if ($murid->user) {
                $updated = false;
                if (!$murid->user->hasRole('student')) {
                    $murid->user->assignRole('student');
                    $updated = true;
                }
                if (!$murid->user->hasRole('murid')) {
                    $murid->user->assignRole('murid');
                    $updated = true;
                }
                if ($updated) {
                    $count++;
                }
            }
        }

        $this->command->info("Assigned student/murid roles to {$count} users");
        $this->command->info("Total students with accounts: {$murids->count()}");
    }
}
