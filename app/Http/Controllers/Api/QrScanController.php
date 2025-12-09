<?php

namespace App\Http\Controllers\Api;

use App\Events\QrCodeScanned;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Murid;
use App\Models\QrCode;
use App\Models\Setting;
use App\Models\JamPelajaran;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QrScanController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        // Find QR Code
        $qrCode = QrCode::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau tidak aktif',
            ], 404);
        }

        // Check if QR code is within validity period
        if ($qrCode->berlaku_dari && $qrCode->berlaku_sampai) {
            $today = now()->toDateString();
            if ($today < $qrCode->berlaku_dari->toDateString() || $today > $qrCode->berlaku_sampai->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code sudah tidak aktif',
                ], 400);
            }
        }

        // Find Murid by QR Code
        $murid = Murid::where('qr_code_id', $qrCode->id)
            ->where('is_active', true)
            ->first();

        if (!$murid) {
            return response()->json([
                'success' => false,
                'message' => 'Murid tidak ditemukan',
            ], 404);
        }

        // Validate time window
        $timeValidation = $this->validateTimeWindow();
        if (!$timeValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $timeValidation['message'],
            ], 400);
        }

        // Check for existing attendance
        $existingAbsensi = Absensi::where('murid_id', $murid->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        // Calculate lateness
        $currentTime = now();
        $checkInTime = $currentTime->format('H:i:s');
        $lateInfo = $this->calculateLateness($currentTime);

        if ($existingAbsensi) {
            // Record already exists - check if QR scan already done
            if ($existingAbsensi->qr_scan_done) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan QR Scan hari ini pada ' . 
                        Carbon::parse($existingAbsensi->qr_scan_time)->format('H:i:s'),
                ], 400);
            }

            // Update existing record - add QR scan
            $existingAbsensi->update([
                'qr_scan_done' => true,
                'qr_scan_time' => now(),
                'is_complete' => true, // Both Manual and QR done
                'check_in_time' => $checkInTime, // Update with QR scan time
                'is_late' => $lateInfo['is_late'],
                'late_duration' => $lateInfo['late_duration'],
                'status' => $lateInfo['is_late'] ? 'Terlambat' : 'Hadir',
            ]);

            $absensi = $existingAbsensi;

            // Create notification if student is late
            if ($lateInfo['is_late']) {
                $notificationService = new NotificationService();
                $notificationService->createLateArrivalNotification($absensi);
            }

            // Broadcast event
            broadcast(new QrCodeScanned($absensi, $murid->name, $absensi->status));

            $message = '✅ Absensi LENGKAP! QR Scan dan Absensi Manual telah dilakukan. Kehadiran Anda terverifikasi penuh.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_complete' => true,
                'data' => [
                    'murid' => $murid->name,
                    'kelas' => $murid->kelas,
                    'status' => $absensi->status,
                    'tanggal' => $absensi->tanggal,
                    'waktu' => $checkInTime,
                    'is_late' => $lateInfo['is_late'],
                    'late_duration' => $lateInfo['late_duration'],
                ],
            ]);

        } else {
            // Create new attendance record - QR scan first
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => $lateInfo['is_late'] ? 'Terlambat' : 'Hadir',
                'kelas' => $murid->kelas,
                'check_in_time' => $checkInTime,
                'is_late' => $lateInfo['is_late'],
                'late_duration' => $lateInfo['late_duration'],
                'qr_scan_done' => true,
                'qr_scan_time' => now(),
                'manual_checkin_done' => false,
                'is_complete' => false, // Not complete yet, need manual check-in
                'first_method' => 'qr_scan',
            ]);

            // Create notification if student is late
            if ($lateInfo['is_late']) {
                $notificationService = new NotificationService();
                $notificationService->createLateArrivalNotification($absensi);
            }

            // Broadcast event
            broadcast(new QrCodeScanned($absensi, $murid->name, $absensi->status));

            $lateMessage = $lateInfo['is_late'] 
                ? ' (Terlambat ' . $lateInfo['late_duration'] . ' menit)'
                : '';

            $message = '⚠️ QR Scan berhasil' . $lateMessage . '. WAJIB lakukan Absensi Manual untuk melengkapi absensi Anda!';

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_complete' => false,
                'data' => [
                    'murid' => $murid->name,
                    'kelas' => $murid->kelas,
                    'status' => $absensi->status,
                    'tanggal' => $absensi->tanggal,
                    'waktu' => $checkInTime,
                    'is_late' => $lateInfo['is_late'],
                    'late_duration' => $lateInfo['late_duration'],
                ],
            ]);
        }
    }

    /**
     * Validate if current time is within allowed check-in window
     */
    private function validateTimeWindow(): array
    {
        // Get time window settings from database
        $checkInStart = Setting::get('check_in_start', '06:00:00');
        $checkInEnd = Setting::get('check_in_end', '08:00:00');

        $currentTime = now()->format('H:i:s');

        if ($currentTime < $checkInStart || $currentTime > $checkInEnd) {
            return [
                'valid' => false,
                'message' => 'Scan hanya dapat dilakukan pada jam ' . 
                    substr($checkInStart, 0, 5) . ' - ' . substr($checkInEnd, 0, 5),
            ];
        }

        return ['valid' => true];
    }

    /**
     * Calculate if student is late and by how many minutes
     */
    private function calculateLateness(Carbon $checkInTime): array
    {
        // Get late threshold from settings (default 07:30:00)
        $lateThreshold = Setting::get('late_threshold', '07:30:00');
        
        $thresholdTime = Carbon::createFromFormat('H:i:s', $lateThreshold);
        $checkTime = Carbon::createFromFormat('H:i:s', $checkInTime->format('H:i:s'));

        if ($checkTime->greaterThan($thresholdTime)) {
            $lateDuration = $thresholdTime->diffInMinutes($checkTime);
            return [
                'is_late' => true,
                'late_duration' => $lateDuration,
            ];
        }

        return [
            'is_late' => false,
            'late_duration' => null,
        ];
    }
}
