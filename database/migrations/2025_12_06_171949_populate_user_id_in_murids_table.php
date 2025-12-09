<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Murid;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration populates user_id for existing Murid records
        // It creates User accounts for Murids that don't have one
        
        // Ensure the 'murid' role exists
        $studentRole = Role::firstOrCreate(['name' => 'murid']);
        
        // Get all Murid records without user_id
        $murids = Murid::whereNull('user_id')->get();
        
        foreach ($murids as $murid) {
            try {
                // Generate email from murid email or create one
                $email = $murid->email ?: $this->generateEmail($murid);
                
                // Check if user with this email already exists
                $user = User::where('email', $email)->first();
                
                if (!$user) {
                    // Create new user account
                    $user = User::create([
                        'name' => $murid->name,
                        'email' => $email,
                        'password' => bcrypt('student123'), // Default password
                        'email_verified_at' => null,
                    ]);
                    
                    // Assign student role
                    $user->assignRole($studentRole);
                }
                
                // Link user to murid
                $murid->user_id = $user->id;
                $murid->save();
                
            } catch (\Exception $e) {
                // Log error but continue with other records
                \Log::error("Error creating user for Murid ID {$murid->id}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set user_id to null for all murids
        Murid::whereNotNull('user_id')->update(['user_id' => null]);
    }
    
    /**
     * Generate email address for murid
     */
    private function generateEmail(Murid $murid): string
    {
        // Generate email from name
        $slug = Str::slug($murid->name);
        $baseEmail = $slug . '@student.school.id';
        
        // Check if email exists and add number if needed
        $email = $baseEmail;
        $counter = 1;
        
        while (User::where('email', $email)->exists()) {
            $email = $slug . $counter . '@student.school.id';
            $counter++;
        }
        
        return $email;
    }
};
