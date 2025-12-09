<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Murid;
use App\Models\User;
use Spatie\Permission\Models\Role;

class StudentRoleAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the 'murid' role exists
        $studentRole = Role::firstOrCreate(['name' => 'murid']);

        // Get all Murid records that have a linked user_id
        $murids = Murid::whereNotNull('user_id')->with('user')->get();

        foreach ($murids as $murid) {
            if ($murid->user) {
                // Assign the 'murid' role to the user if they don't already have it
                if (!$murid->user->hasRole('murid')) {
                    $murid->user->assignRole($studentRole);
                    $this->command->info("Assigned 'murid' role to user: {$murid->user->email}");
                }
            }
        }

        $this->command->info('Student role assignment completed.');
    }
}
