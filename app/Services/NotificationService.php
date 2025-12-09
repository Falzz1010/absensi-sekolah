<?php

namespace App\Services;

use App\Models\StudentNotification;
use App\Models\Absensi;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Create a late arrival notification for a student
     *
     * @param Absensi $absensi The attendance record with late arrival
     * @return StudentNotification
     */
    public function createLateArrivalNotification(Absensi $absensi): StudentNotification
    {
        $lateDuration = $absensi->late_duration ?? 0;
        $checkInTime = $absensi->check_in_time 
            ? Carbon::parse($absensi->check_in_time)->format('H:i')
            : Carbon::parse($absensi->created_at)->format('H:i');
        
        $title = 'Keterlambatan Tercatat';
        $message = sprintf(
            'Anda terlambat %d menit pada tanggal %s pukul %s.',
            $lateDuration,
            Carbon::parse($absensi->tanggal)->format('d/m/Y'),
            $checkInTime
        );

        return StudentNotification::create([
            'murid_id' => $absensi->murid_id,
            'type' => 'late_arrival',
            'title' => $title,
            'message' => $message,
            'data' => [
                'absensi_id' => $absensi->id,
                'date' => $absensi->tanggal,
                'check_in_time' => $checkInTime,
                'late_duration' => $lateDuration,
            ],
        ]);
    }

    /**
     * Create a verification status notification for a student
     *
     * @param Absensi $absensi The attendance record with verification status change
     * @param string $status The new verification status (approved, rejected)
     * @return StudentNotification
     */
    public function createVerificationStatusNotification(Absensi $absensi, string $status): StudentNotification
    {
        $statusText = $status === 'approved' ? 'disetujui' : 'ditolak';
        $title = 'Status Verifikasi Absensi';
        $message = sprintf(
            'Pengajuan absensi Anda pada tanggal %s telah %s.',
            Carbon::parse($absensi->tanggal)->format('d/m/Y'),
            $statusText
        );

        return StudentNotification::create([
            'murid_id' => $absensi->murid_id,
            'type' => 'verification_update',
            'title' => $title,
            'message' => $message,
            'data' => [
                'absensi_id' => $absensi->id,
                'date' => $absensi->tanggal,
                'status' => $status,
                'verified_at' => $absensi->verified_at?->toIso8601String(),
            ],
        ]);
    }
}
