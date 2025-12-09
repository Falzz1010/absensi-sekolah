<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Murid;
use Illuminate\Console\Command;

class SyncExistingMuridUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'murid:sync-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing users with murid role to create Murid records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mencari user dengan role murid yang belum punya record Murid...');

        // Get all users with 'murid' or 'student' role
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['murid', 'student']);
        })->get();

        $this->info("Ditemukan {$users->count()} user dengan role murid/student");

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($users as $user) {
            // Check if Murid record already exists
            $murid = Murid::where('user_id', $user->id)->first();

            if ($murid) {
                // Update existing Murid record
                $murid->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => true,
                ]);
                $updated++;
                $this->line("✓ Updated: {$user->name} ({$user->email})");
            } else {
                // Create new Murid record
                Murid::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'kelas' => null,
                    'kelas_id' => null,
                    'is_active' => true,
                ]);
                $created++;
                $this->line("✓ Created: {$user->name} ({$user->email})");
            }
        }

        $this->newLine();
        $this->info('=== HASIL SYNC ===');
        $this->info("Dibuat baru: {$created}");
        $this->info("Diupdate: {$updated}");
        $this->info("Total: " . ($created + $updated));
        $this->newLine();
        $this->info('✓ Sync selesai!');

        return Command::SUCCESS;
    }
}
