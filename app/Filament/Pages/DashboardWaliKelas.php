<?php

namespace App\Filament\Pages;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Murid;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;

class DashboardWaliKelas extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Dashboard Wali Kelas';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.pages.dashboard-wali-kelas';
    protected static ?string $pollingInterval = '30s';

    public ?array $data = [];
    public $bulan;
    public $tahun;
    public $kelasData = [];
    public $rekapBulanan = [];
    public $statistik = [];

    public static function canAccess(): bool
    {
        // Check if user is guru and is wali kelas
        if (!auth()->user()->hasRole('guru')) {
            return false;
        }

        $guru = Guru::where('user_id', auth()->id())->first();
        if (!$guru) {
            return false;
        }

        // Check if guru is wali kelas
        $isWaliKelas = Kelas::where('wali_kelas_id', $guru->id)->exists();
        return $isWaliKelas;
    }

    public function mount(): void
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;
        
        $this->form->fill([
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
        ]);

        $this->loadData();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('bulan')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ])
                    ->default(now()->month)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadData()),

                Select::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $years = [];
                        for ($i = now()->year - 2; $i <= now()->year + 1; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->default(now()->year)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadData()),
            ])
            ->statePath('data');
    }

    public function loadData(): void
    {
        $bulan = $this->data['bulan'] ?? now()->month;
        $tahun = $this->data['tahun'] ?? now()->year;

        // Get guru's kelas
        $guru = Guru::where('user_id', auth()->id())->first();
        if (!$guru) {
            return;
        }

        $kelas = Kelas::where('wali_kelas_id', $guru->id)->first();
        if (!$kelas) {
            return;
        }

        $this->kelasData = [
            'nama' => $kelas->nama,
            'tingkat' => $kelas->tingkat,
            'jurusan' => $kelas->jurusan,
            'kapasitas' => $kelas->kapasitas,
            'jumlah_murid' => $kelas->murids()->count(),
        ];

        // Get murids in this kelas
        $murids = Murid::where('kelas', $kelas->nama)
            ->where('is_active', true)
            ->get();

        // Calculate rekap bulanan per murid
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        $totalHariKerja = $this->getHariKerja($startDate, $endDate);

        $rekapData = [];
        foreach ($murids as $murid) {
            $absensis = Absensi::where('murid_id', $murid->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();

            $hadir = $absensis->where('status', 'Hadir')->count();
            $sakit = $absensis->where('status', 'Sakit')->count();
            $izin = $absensis->where('status', 'Izin')->count();
            $alfa = $absensis->where('status', 'Alfa')->count();
            $persentase = $totalHariKerja > 0 ? round(($hadir / $totalHariKerja) * 100, 1) : 0;

            $rekapData[] = [
                'murid_id' => $murid->id,
                'nama' => $murid->name,
                'email' => $murid->email,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alfa' => $alfa,
                'total' => $absensis->count(),
                'persentase' => $persentase,
            ];
        }

        // Sort by persentase descending
        usort($rekapData, function ($a, $b) {
            return $b['persentase'] <=> $a['persentase'];
        });

        $this->rekapBulanan = $rekapData;

        // Calculate statistik kelas
        $totalHadir = array_sum(array_column($rekapData, 'hadir'));
        $totalSakit = array_sum(array_column($rekapData, 'sakit'));
        $totalIzin = array_sum(array_column($rekapData, 'izin'));
        $totalAlfa = array_sum(array_column($rekapData, 'alfa'));
        $totalAbsensi = $totalHadir + $totalSakit + $totalIzin + $totalAlfa;
        $rataRataKehadiran = count($rekapData) > 0 
            ? round(array_sum(array_column($rekapData, 'persentase')) / count($rekapData), 1) 
            : 0;

        $this->statistik = [
            'total_murid' => count($rekapData),
            'total_hari_kerja' => $totalHariKerja,
            'total_hadir' => $totalHadir,
            'total_sakit' => $totalSakit,
            'total_izin' => $totalIzin,
            'total_alfa' => $totalAlfa,
            'total_absensi' => $totalAbsensi,
            'rata_rata_kehadiran' => $rataRataKehadiran,
        ];
    }

    private function getHariKerja(Carbon $start, Carbon $end): int
    {
        $count = 0;
        $current = $start->copy();

        while ($current <= $end) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    public function exportExcel()
    {
        // TODO: Implement Excel export
        \Filament\Notifications\Notification::make()
            ->title('Export Excel')
            ->body('Fitur export akan segera tersedia')
            ->info()
            ->send();
    }

    public function exportPdf()
    {
        // TODO: Implement PDF export
        \Filament\Notifications\Notification::make()
            ->title('Export PDF')
            ->body('Fitur export akan segera tersedia')
            ->info()
            ->send();
    }
}
