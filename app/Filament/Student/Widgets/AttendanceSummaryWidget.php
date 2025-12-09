<?php

namespace App\Filament\Student\Widgets;

use App\Models\Absensi;
use App\Models\Murid;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AttendanceSummaryWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        $user = Auth::user();
        $murid = Murid::where('user_id', $user->id)->first();
        
        if (!$murid) {
            return [];
        }
        
        // Get attendance records for past 30 days
        $thirtyDaysAgo = now()->subDays(30);
        $attendances = Absensi::where('murid_id', $murid->id)
            ->where('tanggal', '>=', $thirtyDaysAgo)
            ->get();
        
        // Calculate counts
        $presentCount = $attendances->where('status', 'Hadir')->where('is_late', false)->count();
        $lateCount = $attendances->where('is_late', true)->count();
        $sickCount = $attendances->where('status', 'Sakit')->count();
        $permissionCount = $attendances->where('status', 'Izin')->count();
        $absentCount = $attendances->where('status', 'Alfa')->count();
        $pendingCount = $attendances->where('verification_status', 'pending')->count();
        
        return [
            Stat::make('Hadir', $presentCount)
                ->description('30 hari terakhir')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Terlambat', $lateCount)
                ->description('30 hari terakhir')
                ->descriptionIcon('heroicon-m-clock')
                ->color($lateCount > 3 ? 'warning' : 'gray'),
            
            Stat::make('Sakit', $sickCount)
                ->description('30 hari terakhir')
                ->descriptionIcon('heroicon-m-heart')
                ->color($sickCount > 5 ? 'warning' : 'gray'),
            
            Stat::make('Izin', $permissionCount)
                ->description('30 hari terakhir')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),
            
            Stat::make('Alfa', $absentCount)
                ->description('30 hari terakhir')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($absentCount > 3 ? 'danger' : 'gray'),
            
            Stat::make('Menunggu Verifikasi', $pendingCount)
                ->description('Pengajuan izin/sakit')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 0 ? 'info' : 'gray'),
        ];
    }
}
