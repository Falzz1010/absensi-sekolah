<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RekapMingguan extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '120s';

    // Only show in admin panel
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guru']);
    }

    protected function getStats(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $weeklyData = Absensi::whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalWeekly = $weeklyData->sum();
        $hadir = $weeklyData->get('Hadir', 0);
        $sakit = $weeklyData->get('Sakit', 0);
        $izin = $weeklyData->get('Izin', 0);
        $alfa = $weeklyData->get('Alfa', 0);

        $persentaseHadir = $totalWeekly > 0 ? round(($hadir / $totalWeekly) * 100, 1) : 0;

        return [
            Stat::make('Total Absensi Minggu Ini', $totalWeekly)
                ->description('Periode: ' . $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Kehadiran Minggu Ini', $hadir)
                ->description($persentaseHadir . '% dari total')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([
                    $weeklyData->get('Hadir', 0),
                    $weeklyData->get('Sakit', 0),
                    $weeklyData->get('Izin', 0),
                    $weeklyData->get('Alfa', 0),
                ]),

            Stat::make('Sakit', $sakit)
                ->description('Minggu ini')
                ->descriptionIcon('heroicon-m-heart')
                ->color('warning'),

            Stat::make('Izin', $izin)
                ->description('Minggu ini')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Alfa', $alfa)
                ->description('Minggu ini')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
