<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Filament\Widgets\Widget;

class RankingKehadiranKelas extends Widget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.ranking-kehadiran-kelas';
    protected static ?string $pollingInterval = '120s';

    // Only show in admin panel
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guru']);
    }

    public function getRankingData()
    {
        return Absensi::select('kelas')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir')
            ->selectRaw('ROUND((SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 2) as persentase')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->groupBy('kelas')
            ->orderByDesc('persentase')
            ->get()
            ->toArray();
    }
}
