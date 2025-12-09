<?php

namespace App\Observers;

use App\Events\AbsensiCreated;
use App\Events\AbsensiUpdated;
use App\Models\Absensi;
use App\Services\NotificationService;

class AbsensiObserver
{
    public function created(Absensi $absensi): void
    {
        broadcast(new AbsensiCreated($absensi))->toOthers();
    }

    public function updated(Absensi $absensi): void
    {
        broadcast(new AbsensiUpdated($absensi))->toOthers();
        
        // Check if verification_status was changed
        if ($absensi->isDirty('verification_status')) {
            $newStatus = $absensi->verification_status;
            
            // Only send notification for approved or rejected status
            if (in_array($newStatus, ['approved', 'rejected'])) {
                $notificationService = new NotificationService();
                $notificationService->createVerificationStatusNotification($absensi, $newStatus);
            }
        }
    }
}
