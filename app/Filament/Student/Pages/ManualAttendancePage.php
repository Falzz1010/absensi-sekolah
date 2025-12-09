<?php

namespace App\Filament\Student\Pages;

use App\Models\Absensi;
use App\Models\Murid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ManualAttendancePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static string $view = 'filament.student.pages.manual-attendance-page';

    protected static ?string $navigationLabel = 'Absen Manual';

    protected static ?string $title = 'Konfirmasi Kehadiran Manual';

    protected static ?int $navigationSort = 2;

    public function confirmAttendance(): void
    {
        // Get authenticated student
        $user = Auth::user();
        $murid = Murid::where('user_id', $user->id)->first();

        if (!$murid) {
            Notification::make()
                ->title('Error')
                ->body('Data siswa tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        // Use current date and time
        $tanggal = now()->toDateString();
        $checkInTime = now()->format('H:i:s');

        // Check if attendance already exists for today
        $existingAbsensi = Absensi::where('murid_id', $murid->id)
            ->whereDate('tanggal', $tanggal)
            ->first();

        // Calculate if late based on late_threshold setting
        $isLate = false;
        $lateDuration = null;
        $lateThreshold = \App\Models\Setting::get('late_threshold', '07:30:00');
        $thresholdTime = \Carbon\Carbon::createFromFormat('H:i:s', $lateThreshold);
        $checkTime = \Carbon\Carbon::createFromFormat('H:i:s', $checkInTime);

        if ($checkTime->greaterThan($thresholdTime)) {
            $isLate = true;
            $lateDuration = $thresholdTime->diffInMinutes($checkTime);
        }

        if ($existingAbsensi) {
            // Record already exists - check if manual check-in already done
            if ($existingAbsensi->manual_checkin_done) {
                Notification::make()
                    ->title('Sudah Absen')
                    ->body('Anda sudah melakukan konfirmasi kehadiran manual hari ini pada ' . 
                        \Carbon\Carbon::parse($existingAbsensi->manual_checkin_time)->format('H:i:s'))
                    ->warning()
                    ->send();
                return;
            }

            // Update existing record - add manual check-in
            $existingAbsensi->update([
                'manual_checkin_done' => true,
                'manual_checkin_time' => now(),
                'is_complete' => true, // Both QR and Manual done
                'keterangan' => ($existingAbsensi->keterangan ?? '') . ' | Konfirmasi manual kehadiran',
            ]);

            $message = '✅ Absensi LENGKAP! Anda telah melakukan QR Scan dan Konfirmasi Manual. Kehadiran Anda terverifikasi penuh.';

            Notification::make()
                ->title('Verifikasi Lengkap!')
                ->body($message)
                ->success()
                ->duration(8000)
                ->send();

        } else {
            // Create new attendance record - manual first
            Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $tanggal,
                'status' => $isLate ? 'Terlambat' : 'Hadir',
                'kelas' => $murid->kelas,
                'keterangan' => 'Konfirmasi manual kehadiran',
                'check_in_time' => $checkInTime,
                'is_late' => $isLate,
                'late_duration' => $lateDuration,
                'manual_checkin_done' => true,
                'manual_checkin_time' => now(),
                'qr_scan_done' => false,
                'is_complete' => false, // Not complete yet, need QR scan
                'first_method' => 'manual',
            ]);

            $lateMessage = $isLate ? " Anda terlambat {$lateDuration} menit." : " Anda tepat waktu!";
            $message = '⚠️ Konfirmasi manual berhasil.' . $lateMessage . ' WAJIB scan QR Code untuk melengkapi absensi Anda!';

            Notification::make()
                ->title('Perlu QR Scan!')
                ->body($message)
                ->warning()
                ->duration(8000)
                ->send();
        }

        // Refresh the page to update status
        $this->redirect(static::getUrl());
    }

    public function getTitle(): string
    {
        return 'Konfirmasi Kehadiran Manual';
    }
}
