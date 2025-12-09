<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RekapBulanan extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static ?string $pollingInterval = '120s';
    
    // Only show in admin panel
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guru']);
    }
    
    protected function getStats(): array
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        
        $totalBulanIni = Absensi::whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->count();
            
        $hadirBulanIni = Absensi::whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'Hadir')
            ->count();
            
        $sakitBulanIni = Absensi::whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'Sakit')
            ->count();
            
        $izinBulanIni = Absensi::whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'Izin')
            ->count();
            
        $alfaBulanIni = Absensi::whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'Alfa')
            ->count();
        
        $persentaseHadir = $totalBulanIni > 0 ? round(($hadirBulanIni / $totalBulanIni) * 100, 1) : 0;

        return [
            Stat::make('Total Absensi Bulan Ini', $totalBulanIni)
                ->description(Carbon::now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
            
            Stat::make('Hadir', $hadirBulanIni)
                ->description($persentaseHadir . '% dari total')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Sakit', $sakitBulanIni)
                ->description('Bulan ini')
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),
            
            Stat::make('Izin', $izinBulanIni)
                ->description('Bulan ini')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
            
            Stat::make('Alfa', $alfaBulanIni)
                ->description('Bulan ini')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
