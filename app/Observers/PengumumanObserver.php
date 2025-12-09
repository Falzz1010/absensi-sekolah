<?php

namespace App\Observers;

use App\Models\Murid;
use App\Models\Pengumuman;
use App\Models\User;
use App\Notifications\PengumumanNotification;

class PengumumanObserver
{
    /**
     * Handle the Pengumuman "created" event.
     */
    public function created(Pengumuman $pengumuman): void
    {
        // Only send if active and published
        if (!$pengumuman->is_active || !$pengumuman->published_at) {
            return;
        }

        // Check if published date is in the future
        if ($pengumuman->published_at->isFuture()) {
            return;
        }

        $this->sendNotifications($pengumuman);
    }

    /**
     * Handle the Pengumuman "updated" event.
     */
    public function updated(Pengumuman $pengumuman): void
    {
        // Only send if active and published
        if (!$pengumuman->is_active || !$pengumuman->published_at) {
            return;
        }

        // Check if published date is in the future
        if ($pengumuman->published_at->isFuture()) {
            return;
        }

        // Only send if just activated or just published
        if ($pengumuman->wasChanged(['is_active', 'published_at'])) {
            $this->sendNotifications($pengumuman);
        }
    }

    /**
     * Send notifications to target students
     */
    private function sendNotifications(Pengumuman $pengumuman): void
    {
        if ($pengumuman->target === 'semua') {
            // Send to all students
            $students = User::whereHas('roles', function($query) {
                $query->where('name', 'murid');
            })->get();

            foreach ($students as $student) {
                $student->notify(new PengumumanNotification($pengumuman));
            }
        } else {
            // Send to specific classes
            $kelasArray = $pengumuman->getTargetKelasArray();
            
            if (empty($kelasArray)) {
                return;
            }

            // Get murids from target classes
            $murids = Murid::whereIn('kelas', $kelasArray)
                ->where('is_active', true)
                ->with('user')
                ->get();

            foreach ($murids as $murid) {
                if ($murid->user) {
                    $murid->user->notify(new PengumumanNotification($pengumuman));
                }
            }
        }
    }
}
