<?php

namespace App\Filament\Pages;

use App\Models\Absensi;
use App\Models\Murid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class InputAbsensiKelas extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Input Absensi Kelas';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.pages.input-absensi-kelas';
    protected static ?string $pollingInterval = '30s';

    public ?array $data = [];
    public array $muridList = [];

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['admin', 'guru']);
    }

    public function mount(): void
    {
        $this->form->fill([
            'tanggal' => now()->toDateString(),
            'kelas' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pilih Kelas dan Tanggal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('kelas')
                                    ->label('Pilih Kelas')
                                    ->options([
                                        '10 IPA' => '10 IPA',
                                        '10 IPS' => '10 IPS',
                                        '11 IPA' => '11 IPA',
                                        '11 IPS' => '11 IPS',
                                        '12 IPA' => '12 IPA',
                                        '12 IPS' => '12 IPS',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->loadMurids($state);
                                    }),

                                DatePicker::make('tanggal')
                                    ->label('Tanggal')
                                    ->required()
                                    ->default(now()->toDateString()),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function loadMurids(?string $kelas): void
    {
        if (!$kelas) {
            $this->muridList = [];
            return;
        }

        $this->muridList = Murid::where('kelas', $kelas)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($murid) {
                return [
                    'id' => $murid->id,
                    'name' => $murid->name,
                    'status' => 'Hadir',
                ];
            })
            ->toArray();
    }

    public function updateStatus($muridId, $status): void
    {
        foreach ($this->muridList as $key => $murid) {
            if ($murid['id'] == $muridId) {
                $this->muridList[$key]['status'] = $status;
                break;
            }
        }
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        if (empty($this->muridList)) {
            Notification::make()
                ->title('Pilih kelas terlebih dahulu')
                ->warning()
                ->send();
            return;
        }

        foreach ($this->muridList as $murid) {
            Absensi::updateOrCreate(
                [
                    'murid_id' => $murid['id'],
                    'tanggal' => $data['tanggal'],
                ],
                [
                    'status' => $murid['status'],
                    'kelas' => $data['kelas'],
                ]
            );
        }

        Notification::make()
            ->title('Absensi berhasil disimpan untuk ' . count($this->muridList) . ' murid')
            ->success()
            ->send();

        // Reset form
        $this->muridList = [];
        $this->form->fill([
            'tanggal' => now()->toDateString(),
            'kelas' => null,
        ]);
    }
}
