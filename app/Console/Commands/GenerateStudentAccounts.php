<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Murid;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class GenerateStudentAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:generate-accounts {--force : Force regeneration of existing accounts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate User accounts for existing Murid records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        
        // Ensure the 'murid' role exists
        $studentRole = Role::firstOrCreate(['name' => 'murid']);
        
        // Get all Murid records
        $query = Murid::query();
        
        if (!$force) {
            // Only get murids without user accounts
            $query->whereNull('user_id');
        }
        
        $murids = $query->get();
        
        if ($murids->isEmpty()) {
            $this->info('No Murid records found that need user accounts.');
            return 0;
        }
        
        $this->info("Processing {$murids->count()} Murid records...");
        $progressBar = $this->output->createProgressBar($murids->count());
        $progressBar->start();
        
        $created = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($murids as $murid) {
            try {
                // Skip if user already exists and not forcing
                if ($murid->user_id && !$force) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }
                
                // Generate email from murid email or create one
                $email = $murid->email ?: $this->generateEmail($murid);
                
                // Check if user with this email already exists
                $user = User::where('email', $email)->first();
                
                if (!$user) {
                    // Create new user account
                    $defaultPassword = 'student123'; // Default password
                    
                    $user = User::create([
                        'name' => $murid->name,
                        'email' => $email,
                        'password' => bcrypt($defaultPassword),
                        'email_verified_at' => null, // Require password change on first login
                    ]);
                    
                    // Assign student role
                    $user->assignRole($studentRole);
                    
                    $created++;
                } else {
                    // User exists, just link it
                    if (!$user->hasRole('murid')) {
                        $user->assignRole($studentRole);
                    }
                }
                
                // Link user to murid
                $murid->user_id = $user->id;
                $murid->save();
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("\nError processing Murid ID {$murid->id}: {$e->getMessage()}");
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Account generation completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Created', $created],
                ['Skipped', $skipped],
                ['Errors', $errors],
            ]
        );
        
        if ($created > 0) {
            $this->warn("Default password for new accounts: student123");
            $this->warn("Students should change their password on first login.");
        }
        
        return 0;
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
}
