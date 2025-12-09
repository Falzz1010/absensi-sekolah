<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Murid;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Check if user has 'murid' or 'student' role
        if ($user->hasAnyRole(['murid', 'student'])) {
            // Auto-create Murid record if it doesn't exist
            if (!Murid::where('user_id', $user->id)->exists()) {
                Murid::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'kelas' => null, // Will be set later by admin
                    'kelas_id' => null,
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if user has 'murid' or 'student' role
        if ($user->hasAnyRole(['murid', 'student'])) {
            // Find or create Murid record
            $murid = Murid::where('user_id', $user->id)->first();
            
            if ($murid) {
                // Update existing Murid record
                $murid->update([
                    'name' => $user->name,
                    'email' => $user->email,
                ]);
            } else {
                // Create new Murid record if doesn't exist
                Murid::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'kelas' => null,
                    'kelas_id' => null,
                    'is_active' => true,
                ]);
            }
        } else {
            // If role changed from murid to something else, optionally deactivate
            $murid = Murid::where('user_id', $user->id)->first();
            if ($murid) {
                $murid->update(['is_active' => false]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Optionally deactivate Murid record when User is deleted
        $murid = Murid::where('user_id', $user->id)->first();
        if ($murid) {
            $murid->update(['is_active' => false]);
        }
    }
}
