<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VerificationStatusWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = today();
        
        // Total absensi hari ini (status Hadir)
        $totalToday = Absensi::whereDate('tanggal', $today)
            ->where('status', 'Hadir')
            ->count();
        
        // Absensi lengkap (kedua metode sudah dilakukan)
        $completeToday = Absensi::whereDate('tanggal', $today)
            ->where('status', 'Hadir')
            ->where('is_complete', true)
            ->count();
        
        // Absensi belum lengkap
        $incompleteToday = Absensi::whereDate('tanggal', $today)
            ->where('status', 'Hadir')
            ->where('is_complete', false)
            ->count();
        
        // Hanya QR Scan
        $onlyQr = Absensi::whereDate('tanggal', $today)
            ->where('status', 'Hadir')
            ->where('qr_scan_done', true)
            ->where('manual_checkin_done', false)
            ->count();
        
        // Hanya Manual
        $onlyManual = Absensi::whereDate('tanggal', $today)
            ->where('status', 'Hadir')
            ->where('manual_checkin_done', true)
            ->where('qr_scan_done', false)
            ->count();
        
        // Completion rate
        $completionRate = $totalToday > 0 
            ? round(($completeToday / $totalToday) * 100, 1) 
            : 0;

        return [
            Stat::make('Total Absensi Hari Ini', $totalToday)
                ->description('Siswa yang hadir (semua status)')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 12, 15, 18, 22, 25, $totalToday]),
            
            Stat::make('Verifikasi Lengkap', $completeToday)
                ->description("Tingkat kelengkapan: {$completionRate}%")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([5, 8, 12, 15, 18, 20, $completeToday]),
            
            Stat::make('Belum Lengkap', $incompleteToday)
                ->description('Perlu melengkapi verifikasi')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning')
                ->chart([2, 4, 3, 3, 4, 5, $incompleteToday]),
            
            Stat::make('Hanya QR Scan', $onlyQr)
                ->description('Perlu absensi manual')
                ->descriptionIcon('heroicon-m-qr-code')
                ->color('info')
                ->url(route('filament.admin.resources.absensis.index', [
                    'tableFilters[belum_lengkap_hari_ini][isActive]' => true,
                ])),
            
            Stat::make('Hanya Manual', $onlyManual)
                ->description('Perlu QR scan')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),
        ];
    }
    
    public static function canView(): bool
    {
        return auth()->user()->hasRole(['admin', 'guru']);
    }
}
