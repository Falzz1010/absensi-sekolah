<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Sekolah';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function mount(): void
    {
        $this->form->fill([
            'nama_sekolah' => Setting::get('nama_sekolah', 'SMA Negeri 1'),
            'jam_masuk' => Setting::get('jam_masuk', '07:00'),
            'jam_pulang' => Setting::get('jam_pulang', '15:00'),
            'batas_waktu_absensi' => Setting::get('batas_waktu_absensi', '07:30'),
            'toleransi_terlambat' => Setting::get('toleransi_terlambat', '15'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Sekolah')
                    ->schema([
                        TextInput::make('nama_sekolah')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Jam Pelajaran')
                    ->schema([
                        TimePicker::make('jam_masuk')
                            ->label('Jam Masuk')
                            ->required()
                            ->seconds(false),

                        TimePicker::make('jam_pulang')
                            ->label('Jam Pulang')
                            ->required()
                            ->seconds(false),
                    ])
                    ->columns(2),

                Section::make('Pengaturan Absensi')
                    ->schema([
                        TimePicker::make('batas_waktu_absensi')
                            ->label('Batas Waktu Absensi')
                            ->required()
                            ->seconds(false)
                            ->helperText('Absensi setelah waktu ini dianggap terlambat'),

                        TextInput::make('toleransi_terlambat')
                            ->label('Toleransi Keterlambatan (menit)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(60)
                            ->helperText('Toleransi keterlambatan dalam menit'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'label' => ucwords(str_replace('_', ' ', $key)),
                    'group' => 'general',
                ]
            );
        }

        Notification::make()
            ->title('Berhasil!')
            ->body('Pengaturan sekolah berhasil disimpan')
            ->success()
            ->send();

        // Dispatch alert for better UX
        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Pengaturan Tersimpan!',
            'message' => 'Semua pengaturan sekolah berhasil disimpan',
            'duration' => 5000
        ]);
    }
}
