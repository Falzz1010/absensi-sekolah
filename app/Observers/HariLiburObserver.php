<?php

namespace App\Observers;

use App\Models\HariLibur;
use App\Models\User;
use App\Notifications\HariLiburNotification;

class HariLiburObserver
{
    /**
     * Handle the HariLibur "created" event.
     */
    public function created(HariLibur $hariLibur): void
    {
        // Send notification to all students
        $students = User::whereHas('roles', function($query) {
            $query->where('name', 'murid');
        })->get();

        foreach ($students as $student) {
            $student->notify(new HariLiburNotification($hariLibur, 'created'));
        }
    }

    /**
     * Handle the HariLibur "updated" event.
     */
    public function updated(HariLibur $hariLibur): void
    {
        // Send notification to all students about the update
        $students = User::whereHas('roles', function($query) {
            $query->where('name', 'murid');
        })->get();

        foreach ($students as $student) {
            $student->notify(new HariLiburNotification($hariLibur, 'updated'));
        }
    }

    /**
     * Handle the HariLibur "deleted" event.
     */
    public function deleted(HariLibur $hariLibur): void
    {
        // Optionally send notification about deletion
        // Not implemented to avoid confusion
    }
}
