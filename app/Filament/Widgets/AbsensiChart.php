<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AbsensiChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Kehadiran 7 Hari Terakhir';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '60s';

    // Only show in admin panel
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guru']);
    }

    protected function getData(): array
    {
        $data = $this->getAbsensiData();

        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $data['hadir'],
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Sakit',
                    'data' => $data['sakit'],
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Izin',
                    'data' => $data['izin'],
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Alfa',
                    'data' => $data['alfa'],
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getAbsensiData(): array
    {
        $labels = [];
        $hadir = [];
        $sakit = [];
        $izin = [];
        $alfa = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d M');

            $hadir[] = Absensi::whereDate('tanggal', $date)->where('status', 'Hadir')->count();
            $sakit[] = Absensi::whereDate('tanggal', $date)->where('status', 'Sakit')->count();
            $izin[] = Absensi::whereDate('tanggal', $date)->where('status', 'Izin')->count();
            $alfa[] = Absensi::whereDate('tanggal', $date)->where('status', 'Alfa')->count();
        }

        return [
            'labels' => $labels,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alfa' => $alfa,
        ];
    }
}
