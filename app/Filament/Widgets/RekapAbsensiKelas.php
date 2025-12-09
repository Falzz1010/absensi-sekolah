<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class RekapAbsensiKelas extends Widget
{
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.rekap-absensi-kelas';
    protected static ?string $pollingInterval = '60s'; // Refresh setiap 60 detik

    // Only show in admin panel
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guru']);
    }

    public function getRekapData(): array
    {
        $data = Absensi::query()
            ->whereDate('tanggal', today())
            ->select('kelas', 'status', DB::raw('COUNT(*) as total'))
            ->groupBy('kelas', 'status')
            ->orderBy('kelas')
            ->get()
            ->groupBy('kelas');

        $rekap = [];
        foreach ($data as $kelas => $items) {
            $rekap[$kelas] = [
                'Hadir' => 0,
                'Sakit' => 0,
                'Izin' => 0,
                'Alfa' => 0,
            ];
            
            foreach ($items as $item) {
                $rekap[$kelas][$item->status] = $item->total;
            }
            
            $rekap[$kelas]['Total'] = array_sum($rekap[$kelas]);
        }

        return $rekap;
    }
}
