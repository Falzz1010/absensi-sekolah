<?php

namespace App\Filament\Pages;

use App\Models\Absensi;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;

class LaporanHarian extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Harian';
    protected static string $view = 'filament.pages.laporan-harian';
    protected static ?string $pollingInterval = '30s';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['admin', 'guru']);
    }

    public ?array $data = [];
    public $tanggal;
    public $kelas;
    public $laporanData = [];

    public function mount(): void
    {
        $this->tanggal = now()->toDateString();
        $this->kelas = null;
        $this->form->fill([
            'tanggal' => $this->tanggal,
            'kelas' => $this->kelas,
        ]);
        $this->loadLaporan();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->default(now())
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadLaporan()),

                Select::make('kelas')
                    ->label('Filter Kelas')
                    ->options([
                        'all' => 'Semua Kelas',
                        '10 IPA' => '10 IPA',
                        '10 IPS' => '10 IPS',
                        '11 IPA' => '11 IPA',
                        '11 IPS' => '11 IPS',
                        '12 IPA' => '12 IPA',
                        '12 IPS' => '12 IPS',
                    ])
                    ->default('all')
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadLaporan()),
            ])
            ->statePath('data');
    }

    public function loadLaporan(): void
    {
        $tanggal = $this->data['tanggal'] ?? now()->toDateString();
        $kelas = $this->data['kelas'] ?? 'all';

        $query = Absensi::whereDate('tanggal', $tanggal);

        if ($kelas && $kelas !== 'all') {
            $query->where('kelas', $kelas);
        }

        $absensi = $query->with('murid')->get();

        $this->laporanData = [
            'total' => $absensi->count(),
            'hadir' => $absensi->where('status', 'Hadir')->count(),
            'sakit' => $absensi->where('status', 'Sakit')->count(),
            'izin' => $absensi->where('status', 'Izin')->count(),
            'alfa' => $absensi->where('status', 'Alfa')->count(),
            'persentase_hadir' => $absensi->count() > 0 
                ? round(($absensi->where('status', 'Hadir')->count() / $absensi->count()) * 100, 1) 
                : 0,
            'detail' => $absensi->groupBy('kelas')->map(function ($items, $kelas) {
                return [
                    'kelas' => $kelas,
                    'total' => $items->count(),
                    'hadir' => $items->where('status', 'Hadir')->count(),
                    'sakit' => $items->where('status', 'Sakit')->count(),
                    'izin' => $items->where('status', 'Izin')->count(),
                    'alfa' => $items->where('status', 'Alfa')->count(),
                ];
            })->values()->toArray(),
        ];
    }
}
