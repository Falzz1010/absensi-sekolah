<?php

namespace App\Filament\Widgets;

use App\Models\Murid;
use App\Models\Guru;
use App\Models\Absensi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s'; // Refresh setiap 30 detik
    
    // Only show in admin panel
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guru']);
    }
    
    protected function getStats(): array
    {
        $totalMurid = Murid::count();
        $totalGuru = Guru::count();
        $hadirHariIni = Absensi::whereDate('tanggal', today())
            ->where('status', 'Hadir')
            ->count();
        $totalAbsensiHariIni = Absensi::whereDate('tanggal', today())->count();
        $persentaseKehadiran = $totalAbsensiHariIni > 0 
            ? round(($hadirHariIni / $totalAbsensiHariIni) * 100, 1) 
            : 0;

        return [
            Stat::make('Total Murid', $totalMurid)
                ->description('Murid terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([7, 12, 15, 18, 22, 25, $totalMurid]),
            
            Stat::make('Total Guru', $totalGuru)
                ->description('Guru aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info')
                ->chart([3, 5, 7, 8, 10, 12, $totalGuru]),
            
            Stat::make('Kehadiran Hari Ini', $hadirHariIni . ' / ' . $totalAbsensiHariIni)
                ->description($persentaseKehadiran . '% hadir')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($persentaseKehadiran >= 80 ? 'success' : ($persentaseKehadiran >= 60 ? 'warning' : 'danger'))
                ->chart([65, 70, 75, 80, 85, 90, $persentaseKehadiran]),
        ];
    }
}
